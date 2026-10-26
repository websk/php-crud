import {expect, test} from '@playwright/test';

function collectPageErrors(page) {
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    return errors;
}

test('required CRUD fields are highlighted and block invalid submission', async ({page}) => {
    const pageErrors = collectPageErrors(page);
    await page.goto('/__browser-test');

    const field = page.locator('#required-field');
    await expect(field).not.toHaveClass(/required-class/);

    page.once('dialog', async (dialog) => {
        expect(dialog.message()).toContain('required_field');
        await dialog.accept();
    });
    await page.locator('#required-submit').click();
    expect(await page.evaluate(() => window.requiredFormSubmissions)).toBe(0);
    await expect(field).toHaveClass(/required-class/);

    await field.fill('valid');
    await field.press('Tab');
    await expect(field).not.toHaveClass(/required-class/);
    await page.locator('#required-submit').click();
    expect(await page.evaluate(() => window.requiredFormSubmissions)).toBe(1);
    expect(pageErrors).toEqual([]);
});

test('Ace widgets load every declared mode and synchronize submitted text', async ({page}) => {
    const pageErrors = collectPageErrors(page);
    await page.goto('/__browser-test');

    const editors = page.locator('#ace-fixtures .ace_editor');
    await expect(editors).toHaveCount(7);
    const expectedModes = [
        'ace/mode/html',
        'ace/mode/javascript',
        'ace/mode/json',
        'ace/mode/php',
        'ace/mode/sql',
        'ace/mode/xml',
        'ace/mode/text',
    ];
    await expect.poll(() => editors.evaluateAll((nodes) => (
        nodes.map((node) => window.ace.edit(node).getSession().getMode().$id)
    ))).toEqual(expectedModes);

    await editors.first().evaluate((node) => {
        window.ace.edit(node).setValue('<p>updated</p>', -1);
    });
    await expect(page.locator('textarea[name="ace_html"]')).toHaveValue('<p>updated</p>');
    expect(pageErrors).toEqual([]);
});

test('reference picker and remote autocomplete select and clear values', async ({page}) => {
    const pageErrors = collectPageErrors(page);
    await page.goto('/__browser-test');

    const reference = page.locator('#reference-fixture');
    await reference.locator('[data-bs-toggle="modal"]').click();
    const referenceModal = reference.locator('.modal');
    await expect(referenceModal).toHaveClass(/show/);
    await referenceModal.locator('.js-ajax-form-select').click();
    await expect(reference.locator('input[name="reference_id"]')).toHaveValue('7');
    await expect(reference.locator('[id$="_text"]')).toHaveText('Selected company');
    await expect(reference.locator('input[name="reference_id___is_null"]')).toHaveValue('');

    await reference.locator('button[id$="_btn_is_null"]').click();
    await expect(reference.locator('input[name="reference_id"]')).toHaveValue('');
    await expect(reference.locator('[id$="_text"]')).toHaveText('');
    await expect(reference.locator('input[name="reference_id___is_null"]')).toHaveValue('1');

    const autocomplete = page.locator('#autocomplete-form');
    await expect(autocomplete.locator('.token[data-value="2"]')).toHaveText('Configured company');
    await autocomplete.locator('.token-search input').pressSequentially('Sea');
    const searchedOption = page.locator('.tokenize-dropdown a[data-value="3"]');
    await expect(searchedOption).toBeVisible();
    await expect(searchedOption).toContainText('Searched company');
    await searchedOption.click();
    await expect(autocomplete.locator('.token[data-value="3"]')).toHaveText('Searched company');

    await autocomplete.locator('.token[data-value="2"] .dismiss').click();
    await expect(autocomplete.locator('.token[data-value="2"]')).toHaveCount(0);
    expect(await autocomplete.locator('select[name="companies[]"]').evaluate((select) => (
        Array.from(select.selectedOptions, (option) => option.value)
    ))).toEqual(['3']);
    expect(pageErrors).toEqual([]);
});

test('Magnific Popup opens the inline CRUD form', async ({page}) => {
    const pageErrors = collectPageErrors(page);
    await page.goto('/__browser-test');

    await page.locator('#popup-fixture > button').click();
    const popup = page.locator('.mfp-wrap');
    await expect(popup).toBeVisible();
    const dialog = popup.getByRole('dialog', {name: 'Create record'});
    await expect(dialog).toBeVisible();
    const popupInput = dialog.locator('input[name="popup_value"]');
    await expect(popupInput).toHaveValue('ready');
    await expect(popupInput).toBeFocused();
    await popup.locator('.mfp-close').click();
    await expect(popup).toHaveCount(0);
    expect(pageErrors).toEqual([]);
});

test('demo layout dependencies load from managed local assets', async ({page}) => {
    const pageErrors = collectPageErrors(page);
    const requestedUrls = [];
    page.on('request', (request) => requestedUrls.push(request.url()));
    await page.goto('/__browser-test');

    expect(await page.evaluate(() => ({
        bootstrap: window.bootstrap.Tooltip.VERSION,
        jquery: window.jQuery.fn.jquery,
        jqueryUi: window.jQuery.ui.version,
        validation: typeof window.jQuery.fn.validate,
    }))).toEqual({
        bootstrap: '5.3.8',
        jquery: '3.7.1',
        jqueryUi: '1.14.2',
        validation: 'function',
    });

    const requiredAssets = [
        '/assets/libraries/jquery/jquery.min.js',
        '/assets/libraries/jquery-ui/jquery-ui.min.js',
        '/assets/libraries/bootstrap/js/bootstrap.bundle.min.js',
        '/assets/libraries/bootstrap/css/bootstrap.min.css',
        '/assets/libraries/jquery-validation/jquery.validate.min.js',
        '/assets/libraries/font-awesome/css/all.min.css',
        '/assets/libraries/font-awesome/css/v4-shims.min.css',
    ];
    for (const asset of requiredAssets) {
        expect(requestedUrls.some((url) => new URL(url).pathname === asset)).toBe(true);
    }
    expect(requestedUrls.every((url) => new URL(url).origin === 'http://127.0.0.1:8123')).toBe(true);
    await expect(page.locator('#font-awesome-fixture')).toHaveCSS('font-family', /Font Awesome/);
    expect(pageErrors).toEqual([]);
});

test('clickable table rows keep a single accessible link', async ({page}) => {
    await page.goto('/__browser-test');

    const row = page.locator('#row-link-fixture tbody tr');
    await expect(row.locator('a')).toHaveCount(1);
    await page.locator('#row-secondary-cell').click();
    await expect(page).toHaveURL(/#__row-target$/);
});
