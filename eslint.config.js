const formattingRules = {
    'array-bracket-spacing': ['error', 'never'],
    'comma-dangle': ['error', 'always-multiline'],
    'comma-spacing': 'error',
    'eol-last': ['error', 'always'],
    indent: ['error', 4, {SwitchCase: 1}],
    'keyword-spacing': 'error',
    'no-multiple-empty-lines': ['error', {max: 1, maxEOF: 0}],
    'no-trailing-spaces': 'error',
    'object-curly-spacing': ['error', 'never'],
    quotes: ['error', 'single', {allowTemplateLiterals: true}],
    semi: ['error', 'always'],
    'space-before-blocks': 'error',
};

export default [
    {
        files: [
            'eslint.config.js',
            'playwright.config.js',
            'scripts/**/*.mjs',
            'tests/browser/**/*.js',
        ],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: {
                console: 'readonly',
                document: 'readonly',
                FormData: 'readonly',
                process: 'readonly',
                URL: 'readonly',
                window: 'readonly',
            },
        },
        rules: {
            ...formattingRules,
            'no-undef': 'error',
            'no-unused-vars': ['error', {argsIgnorePattern: '^_'}],
        },
    },
];
