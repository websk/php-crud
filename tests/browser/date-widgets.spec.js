import {expect, test} from '@playwright/test';

test('native date widgets preserve their browser and submission contracts', async ({page}) => {
    const pageErrors = [];
    page.on('pageerror', (error) => pageErrors.push(error.message));

    await page.goto('/__browser-test');

    const form = page.locator('#date-widget-form');
    const date = form.locator('input[type="date"][name="date"]');
    const dateTime = form.locator('input[type="datetime-local"][data-test="date-time"]');
    const timestamp = form.locator('input[type="datetime-local"][data-test="timestamp"]');

    await expect(date).toHaveValue('2026-10-20');
    await date.fill('2026-11-02');

    await expect(dateTime).toHaveJSProperty('required', true);
    await dateTime.fill('');
    expect(await dateTime.evaluate((input) => ({
        valid: input.checkValidity(),
        valueMissing: input.validity.valueMissing,
    }))).toEqual({valid: false, valueMissing: true});
    await dateTime.fill('2026-11-02T14:15:16');
    await expect(form.locator('input[type="hidden"][name="date_time"]')).toHaveValue('2026-11-02 14:15:16');

    const nullable = form.locator('input[type="checkbox"][name="date_time___is_null"]');
    await nullable.check();
    await expect(dateTime).toHaveValue('');
    await expect(form.locator('input[type="hidden"][name="date_time"]')).toHaveValue('');
    await dateTime.fill('2026-12-03T08:09:10');
    await expect(nullable).not.toBeChecked();

    await timestamp.fill('2026-11-02T14:15:16');
    const expectedTimestamp = await timestamp.evaluate((input) => (
        Math.floor(new Date(input.value).getTime() / 1000).toString()
    ));
    expect(expectedTimestamp).toBe('1793618116');
    await expect(form.locator('input[type="hidden"][name="timestamp"]')).toHaveValue(expectedTimestamp);

    const disabledTimestamp = form.locator('input[type="datetime-local"][data-test="disabled-timestamp"]');
    await expect(disabledTimestamp).toBeDisabled();
    await expect(form.locator('input[type="hidden"][name="disabled_timestamp"]')).toHaveValue('1792474245');

    const values = await form.evaluate((element) => Object.fromEntries(new FormData(element)));
    expect(values.date).toBe('2026-11-02');
    expect(values.date_time).toBe('2026-12-03 08:09:10');
    expect(values.timestamp).toBe(expectedTimestamp);
    expect(values.disabled_timestamp).toBe('1792474245');
    expect(pageErrors).toEqual([]);
});

test('Bootstrap 5 bundle drives collapse and modal components', async ({page}) => {
    const pageErrors = [];
    page.on('pageerror', (error) => pageErrors.push(error.message));

    await page.goto('/__browser-test');

    expect(await page.evaluate(() => window.bootstrap.Tooltip.VERSION)).toBe('5.3.8');

    const collapse = page.locator('#bootstrap-collapse');
    await expect(collapse).not.toHaveClass(/show/);
    await page.locator('#collapse-toggle').click();
    await expect(collapse).toHaveClass(/show/);

    const modal = page.locator('#bootstrap-modal');
    await page.locator('#modal-open').click();
    await expect(modal).toHaveClass(/show/);
    await expect(modal).toBeVisible();
    await expect.poll(() => page.evaluate(() => window.bootstrapModalShown)).toBe(true);
    await page.locator('#modal-close').click();
    await expect(modal).not.toHaveClass(/show/);
    await expect(modal).toBeHidden();

    expect(pageErrors).toEqual([]);
});

test('locally built legacy widget assets initialize without CDN access', async ({page}) => {
    const pageErrors = [];
    page.on('pageerror', (error) => pageErrors.push(error.message));

    await page.goto('/__browser-test');

    await expect(page.locator('#tokenize-fixture + .tokenize .token')).toHaveText('First');
    await expect(page.locator('#medium-editor-fixture')).toHaveAttribute('contenteditable', 'true');
    expect(await page.locator('#upload-fixture').evaluate((input) => (
        Boolean(window.jQuery(input).data('blueimp-fileupload'))
    ))).toBe(true);
    expect(pageErrors).toEqual([]);
});
