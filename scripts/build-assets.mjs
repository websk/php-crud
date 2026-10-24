import {createHash} from 'node:crypto';
import {
    access,
    cp,
    mkdir,
    mkdtemp,
    readFile,
    readdir,
    rm,
    stat,
} from 'node:fs/promises';
import {tmpdir} from 'node:os';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const nodeModulesRoot = path.join(projectRoot, 'node_modules');
const assetsRoot = path.join(projectRoot, 'public/assets/libraries');

const managedDirectories = [
    'ace',
    'blueimp-file-upload',
    'bootstrap',
    'crud',
    'font-awesome',
    'jquery',
    'jquery-ui',
    'jquery-validation',
    'medium-editor',
    'tokenize2',
];

const obsoleteDirectories = [
    'bootstrap-datetimepicker',
    'fancybox',
    'moment',
];

const copies = [
    ['blueimp-file-upload/css/jquery.fileupload.css', 'blueimp-file-upload/css/jquery.fileupload.css'],
    ['blueimp-file-upload/js/jquery.fileupload.js', 'blueimp-file-upload/js/jquery.fileupload.js'],
    ['blueimp-file-upload/js/jquery.fileupload-process.js', 'blueimp-file-upload/js/jquery.fileupload-process.js'],
    ['blueimp-file-upload/js/jquery.fileupload-validate.js', 'blueimp-file-upload/js/jquery.fileupload-validate.js'],
    ['bootstrap/dist/js/bootstrap.bundle.min.js', 'bootstrap/js/bootstrap.min.js'],
    ['bootstrap/dist/css/bootstrap.min.css', 'bootstrap/css/bootstrap.min.css'],
    ['jquery/dist/jquery.min.js', 'jquery/jquery.min.js'],
    ['jquery-ui/dist/jquery-ui.min.js', 'jquery-ui/jquery-ui.min.js'],
    ['jquery-ui/dist/themes/base/jquery-ui.min.css', 'jquery-ui/themes/base/jquery-ui.min.css'],
    ['jquery-ui/dist/themes/base/images', 'jquery-ui/themes/base/images'],
    ['jquery-validation/dist/jquery.validate.min.js', 'jquery-validation/jquery.validate.min.js'],
    ['medium-editor/dist/js/medium-editor.min.js', 'medium-editor/js/medium-editor.min.js'],
    ['medium-editor/dist/css/medium-editor.min.css', 'medium-editor/css/medium-editor.min.css'],
    ['medium-editor/dist/css/themes/default.min.css', 'medium-editor/css/themes/default.min.css'],
    ['tokenize2/dist/tokenize2.min.js', 'tokenize2/tokenize2.min.js'],
    ['tokenize2/dist/tokenize2.min.css', 'tokenize2/tokenize2.min.css'],
    ['tokenize2/LICENSE', 'tokenize2/LICENSE'],
    ['@fortawesome/fontawesome-free/css/all.min.css', 'font-awesome/css/all.min.css'],
    ['@fortawesome/fontawesome-free/css/v4-shims.min.css', 'font-awesome/css/v4-shims.min.css'],
    ['@fortawesome/fontawesome-free/webfonts', 'font-awesome/webfonts'],
    ['ace-builds/src-min/ace.js', 'ace/ace.js'],
    ['ace-builds/src-min/mode-html.js', 'ace/mode-html.js'],
    ['ace-builds/src-min/worker-html.js', 'ace/worker-html.js'],
    ['ace-builds/src-min/mode-sql.js', 'ace/mode-sql.js'],
    ['ace-builds/src-min/mode-php.js', 'ace/mode-php.js'],
    ['ace-builds/src-min/mode-xml.js', 'ace/mode-xml.js'],
    ['ace-builds/src-min/worker-php.js', 'ace/worker-php.js'],
    ['ace-builds/src-min/worker-xml.js', 'ace/worker-xml.js'],
    ['ace-builds/src-min/mode-text.js', 'ace/mode-text.js'],
    ['ace-builds/src-min/mode-javascript.js', 'ace/mode-javascript.js'],
    ['ace-builds/src-min/worker-javascript.js', 'ace/worker-javascript.js'],
    ['ace-builds/src-min/mode-json.js', 'ace/mode-json.js'],
    ['ace-builds/src-min/worker-json.js', 'ace/worker-json.js'],
    ['ace-builds/src-min/ext-searchbox.js', 'ace/ext-searchbox.js'],
];

const projectCopies = [
    ['resources/css/crud.css', 'crud/crud.css'],
];

async function assertSourcesExist() {
    for (const [source] of copies) {
        try {
            await access(path.join(nodeModulesRoot, source));
        } catch {
            throw new Error(`Missing source asset: node_modules/${source}. Run npm ci first.`);
        }
    }

    for (const [source] of projectCopies) {
        try {
            await access(path.join(projectRoot, source));
        } catch {
            throw new Error(`Missing source asset: ${source}.`);
        }
    }
}

async function build(targetRoot) {
    await mkdir(targetRoot, {recursive: true});

    for (const directory of [...managedDirectories, ...obsoleteDirectories]) {
        await rm(path.join(targetRoot, directory), {recursive: true, force: true});
    }

    for (const [source, destination] of copies) {
        const sourcePath = path.join(nodeModulesRoot, source);
        const destinationPath = path.join(targetRoot, destination);
        const sourceStat = await stat(sourcePath);

        await mkdir(path.dirname(destinationPath), {recursive: true});
        await cp(sourcePath, destinationPath, {
            recursive: sourceStat.isDirectory(),
            force: true,
        });
    }

    for (const [source, destination] of projectCopies) {
        const sourcePath = path.join(projectRoot, source);
        const destinationPath = path.join(targetRoot, destination);

        await mkdir(path.dirname(destinationPath), {recursive: true});
        await cp(sourcePath, destinationPath, {force: true});
    }
}

async function listFiles(root) {
    const files = [];

    async function walk(directory) {
        for (const entry of await readdir(directory, {withFileTypes: true})) {
            const absolutePath = path.join(directory, entry.name);
            if (entry.isDirectory()) {
                await walk(absolutePath);
            } else if (entry.isFile()) {
                files.push(path.relative(root, absolutePath));
            }
        }
    }

    await walk(root);
    return files.sort();
}

async function digest(filePath) {
    return createHash('sha256').update(await readFile(filePath)).digest('hex');
}

async function compareTrees(expectedRoot, actualRoot) {
    const expectedFiles = await listFiles(expectedRoot);
    const actualFiles = await listFiles(actualRoot);

    if (expectedFiles.join('\n') !== actualFiles.join('\n')) {
        const expected = new Set(expectedFiles);
        const actual = new Set(actualFiles);
        const missing = expectedFiles.filter((file) => !actual.has(file));
        const unexpected = actualFiles.filter((file) => !expected.has(file));
        throw new Error(`Asset file list differs. Missing: ${missing.join(', ') || 'none'}. Unexpected: ${unexpected.join(', ') || 'none'}.`);
    }

    for (const file of expectedFiles) {
        if (await digest(path.join(expectedRoot, file)) !== await digest(path.join(actualRoot, file))) {
            throw new Error(`Generated asset differs: ${file}`);
        }
    }

    return expectedFiles.length;
}

async function assertNoServerFiles(root) {
    const forbiddenExtensions = new Set(['.cgi', '.phar', '.php', '.phtml']);
    const files = await listFiles(root);
    const forbidden = files.filter((file) => forbiddenExtensions.has(path.extname(file).toLowerCase()));

    if (forbidden.length > 0) {
        throw new Error(`Server-side files found in generated assets: ${forbidden.join(', ')}`);
    }

    return files.length;
}

async function assertManagedAssetsSafe(root) {
    let count = 0;
    for (const directory of managedDirectories) {
        count += await assertNoServerFiles(path.join(root, directory));
    }
    return count;
}

async function assertObsoleteAssetsAbsent(root) {
    for (const directory of obsoleteDirectories) {
        try {
            await access(path.join(root, directory));
            throw new Error(`Obsolete asset directory still exists: ${directory}`);
        } catch (error) {
            if (!error || error.code !== 'ENOENT') {
                throw error;
            }
        }
    }
}

await assertSourcesExist();

if (process.argv.includes('--check')) {
    const temporaryRoot = await mkdtemp(path.join(tmpdir(), 'php-crud-assets-'));
    try {
        await build(temporaryRoot);
        let count = 0;
        for (const directory of managedDirectories) {
            count += await compareTrees(
                path.join(temporaryRoot, directory),
                path.join(assetsRoot, directory),
            );
        }
        await assertManagedAssetsSafe(temporaryRoot);
        await assertNoServerFiles(path.join(projectRoot, 'public', 'assets'));
        await assertObsoleteAssetsAbsent(assetsRoot);
        console.log(`Assets are complete, free of server-side files, and reproducible: ${count} files.`);
    } finally {
        await rm(temporaryRoot, {recursive: true, force: true});
    }
} else {
    await build(assetsRoot);
    const count = await assertManagedAssetsSafe(assetsRoot);
    await assertNoServerFiles(path.join(projectRoot, 'public', 'assets'));
    await assertObsoleteAssetsAbsent(assetsRoot);
    console.log(`Built ${count} managed asset files.`);
}
