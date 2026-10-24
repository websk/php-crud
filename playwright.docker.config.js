import {defineConfig} from '@playwright/test';

export default defineConfig({
    testDir: './tests/browser-docker',
    fullyParallel: false,
    workers: 1,
    use: {
        baseURL: process.env.CRUD_DEMO_BASE_URL ?? 'https://php-crud.devbox',
        browserName: 'chromium',
        channel: process.env.PLAYWRIGHT_CHANNEL,
        headless: true,
        ignoreHTTPSErrors: true,
        timezoneId: 'Europe/Moscow',
    },
});
