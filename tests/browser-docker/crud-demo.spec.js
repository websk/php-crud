import {expect, test} from '@playwright/test';

function monitorBrowser(page) {
    const problems = [];

    page.on('console', (message) => {
        if (message.type() === 'error') {
            problems.push(`console: ${message.text()}`);
        }
    });
    page.on('pageerror', (error) => problems.push(`pageerror: ${error.message}`));
    page.on('requestfailed', (request) => {
        problems.push(`requestfailed: ${request.method()} ${request.url()} (${request.failure()?.errorText})`);
    });

    return problems;
}

async function expectValidCrudDom(page) {
    const problems = await page.evaluate(() => {
        const result = [];
        const ids = [...document.querySelectorAll('[id]')].map((element) => element.id);
        const duplicateIds = [...new Set(ids.filter((id, index) => id && ids.indexOf(id) !== index))];
        const deprecatedClasses = [
            'btn-default',
            'col-sm-offset-4',
            'control-label',
            'form-group',
            'help-block',
            'pull-left',
            'pull-right',
            'text-right',
        ];

        if (ids.includes('')) {
            result.push('empty id attribute');
        }
        if (duplicateIds.length > 0) {
            result.push(`duplicate ids: ${duplicateIds.join(', ')}`);
        }
        for (const className of deprecatedClasses) {
            if (document.querySelector(`.${className}`)) {
                result.push(`deprecated Bootstrap class: ${className}`);
            }
        }
        for (const label of document.querySelectorAll('label')) {
            if (!label.htmlFor && !label.querySelector('input, select, textarea')) {
                result.push(`unassociated label: ${label.textContent.trim()}`);
            }
        }
        for (const control of document.querySelectorAll('input:not([type="hidden"]), select, textarea')) {
            if (!control.labels?.length && !control.getAttribute('aria-label') && !control.getAttribute('aria-labelledby')) {
                result.push(`unlabelled control: ${control.getAttribute('name') || control.tagName.toLowerCase()}`);
            }
        }

        return result;
    });

    expect(problems).toEqual([]);
}

test('real CRUD list exercises popup, Ace and multiple autocomplete', async ({page}) => {
    const problems = monitorBrowser(page);
    await page.goto('/demo_users');
    await expectValidCrudDom(page);

    await expect(page.getByRole('heading', {name: 'Пользователи'})).toBeVisible();
    await expect(page.locator('.table tbody tr')).not.toHaveCount(0);
    await expect(page.locator('.table tbody tr').first().locator('a')).toHaveCount(1);
    const deleteButton = page.getByRole('button', {name: 'Удалить'});
    await expect(deleteButton).toHaveClass(/btn-outline-danger/);
    await expect(deleteButton).toHaveCSS('color', 'rgb(220, 53, 69)');

    await page.getByRole('button', {name: 'Создать'}).click();
    const popup = page.locator('.mfp-wrap');
    const dialog = popup.getByRole('dialog', {name: 'Создать'});
    const createForm = dialog.locator('#demo_user_create');
    await expect(popup).toBeVisible();
    await expect(dialog).toBeVisible();
    await expect(createForm).toBeVisible();
    await expect(createForm.locator('input[name="name"]')).toHaveAttribute('required', '');
    await expect(createForm.locator('input[name="email"]')).toHaveAttribute('required', '');
    const requiredName = createForm.getByRole('textbox', {name: 'Имя на сайте'});
    await expect(requiredName).toBeFocused();
    await expect(requiredName).not.toHaveCSS('border-color', 'rgb(220, 53, 69)');
    await requiredName.press('Tab');
    await expect(requiredName).toHaveCSS('border-color', 'rgb(220, 53, 69)');
    await expect(createForm.getByRole('button', {name: 'Очистить'})).toHaveClass(/btn-outline-danger/);

    const popupAce = createForm.locator('.ace_editor');
    await expect(popupAce).toBeVisible();
    expect(await popupAce.evaluate((node) => window.ace.edit(node).getSession().getMode().$id))
        .toBe('ace/mode/text');
    await popupAce.evaluate((node) => window.ace.edit(node).setValue('Popup comment', -1));
    await expect(createForm.locator('textarea[name="comment"]')).toHaveValue('Popup comment');
    await popup.locator('.mfp-close').click();
    await expect(popup).toHaveCount(0);

    const companyFilter = page.locator('select[name="company_id[]"]');
    const tokenize = companyFilter.locator('+ .tokenize');
    await expect(tokenize.locator('.placeholder')).toHaveCSS('background-color', 'rgba(0, 0, 0, 0)');
    await expect(tokenize.locator('.placeholder')).toHaveCSS('opacity', '1');
    await tokenize.locator('.token-search input').pressSequentially('ks');
    const result = page.locator('.tokenize-dropdown a[data-value="4"]');
    await expect(result).toHaveText('kssnet');
    await Promise.all([
        page.waitForResponse((response) => (
            new URL(response.url()).pathname === '/demo_users'
            && response.request().method() === 'POST'
            && response.ok()
        )),
        result.click(),
    ]);
    await expect(page.locator('select[name="company_id[]"] + .tokenize .token[data-value="4"]'))
        .toHaveText('kssnet');
    expect(problems).toEqual([]);
});

test('real CRUD edit form initializes and synchronizes Ace', async ({page}) => {
    const problems = monitorBrowser(page);
    await page.goto('/demo_users/1');
    await expectValidCrudDom(page);

    await expect(page.getByRole('heading', {name: 'Редактирование профиля'})).toBeVisible();
    const form = page.locator('#user_edit');
    await expect(form).toBeVisible();
    await expect(form.locator('input[name="birthday"][type="date"]')).toBeVisible();
    await expect(form.locator('input[name="company_id"]')).toHaveCount(1);

    const ace = form.locator('.ace_editor');
    await expect(ace).toBeVisible();
    expect(await ace.evaluate((node) => window.ace.edit(node).getSession().getMode().$id))
        .toBe('ace/mode/text');
    await ace.evaluate((node) => window.ace.edit(node).setValue('Edited without submit', -1));
    await expect(form.locator('textarea[name="comment"]')).toHaveValue('Edited without submit');
    expect(problems).toEqual([]);
});

test('real CRUD forms remain usable on a mobile viewport', async ({page}) => {
    const problems = monitorBrowser(page);
    await page.setViewportSize({width: 390, height: 844});
    await page.goto('/demo_users');

    const createButton = page.getByRole('button', {name: 'Создать'});
    const filters = page.locator('.crud-filters');
    const createBox = await createButton.boundingBox();
    const filtersBox = await filters.boundingBox();
    expect(createBox).not.toBeNull();
    expect(filtersBox).not.toBeNull();
    expect(filtersBox.y).toBeGreaterThanOrEqual(createBox.y + createBox.height);

    const responsiveTable = page.locator('.table-responsive');
    expect(await responsiveTable.evaluate((element) => element.scrollWidth > element.clientWidth)).toBe(true);
    await expect(responsiveTable.locator('tbody td').first()).toHaveCSS('white-space', 'nowrap');

    await createButton.click();
    const popup = page.locator('.mfp-wrap');
    await expect(popup).toBeVisible();
    expect(await popup.evaluate((element) => element.scrollHeight > element.clientHeight)).toBe(true);
    await expect(popup.locator('.crud-ace-editor')).toHaveCSS('min-height', '320px');
    const saveButton = popup.getByRole('button', {name: 'Сохранить'});
    await saveButton.scrollIntoViewIfNeeded();
    await expect(saveButton).toBeVisible();

    expect(problems).toEqual([]);
});
