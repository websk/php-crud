import {defineConfig} from '@playwright/test';

const host = '127.0.0.1';
const port = 8123;

export default defineConfig({
    testDir: './tests/browser',
    testMatch: '**/*.spec.js',
    fullyParallel: false,
    workers: 1,
    use: {
        baseURL: `http://${host}:${port}`,
        browserName: 'chromium',
        channel: process.env.PLAYWRIGHT_CHANNEL,
        headless: true,
        timezoneId: 'Europe/Moscow',
    },
    webServer: {
        command: `php -d date.timezone=Europe/Moscow -S ${host}:${port} -t public tests/browser/router.php`,
        url: `http://${host}:${port}/__browser-test`,
        reuseExistingServer: false,
        timeout: 30_000,
    },
});
