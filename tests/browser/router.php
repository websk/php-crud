<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDMagnificPopup;
use WebSK\CRUD\Form\CRUDFormScript;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetAceTextarea;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetDate;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetDateTime;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTimestamp;
use WebSK\CRUD\Table\Filters\CRUDTableFilterMultipleAutocomplete;
use WebSK\Entity\InterfaceEntity;

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
if (!is_string($requestUri)) {
    throw new UnexpectedValueException('REQUEST_URI must be a string.');
}
$path = parse_url($requestUri, PHP_URL_PATH);
if ($path === '/__browser-test/reference-picker') {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<button class="js-ajax-form-select" data-id="7" data-title="Selected company">Select company</button>';
    return true;
}
if ($path === '/__browser-test/company-options') {
    $id = $_GET['id'] ?? null;
    $options = $id !== null
        ? [['id' => 2, 'name' => 'Configured company']]
        : [['id' => 3, 'name' => 'Searched company']];
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($options, JSON_THROW_ON_ERROR);
    return true;
}
if ($path !== '/__browser-test') {
    return false;
}

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$entity = new class(
    date: '2026-10-20 08:30:45',
    date_time: '2026-10-20 08:30:45',
    timestamp: 1792474245,
    disabled_timestamp: 1792474245,
) implements InterfaceEntity {
    public function __construct(
        private ?string $date,
        private ?string $date_time,
        private ?int $timestamp,
        private ?int $disabled_timestamp,
        private string $ace_html = '<strong>html</strong>',
        private string $ace_javascript = 'const value = 1;',
        private string $ace_json = '{"value":1}',
        private string $ace_php = '<?php echo 1;',
        private string $ace_sql = 'SELECT 1;',
        private string $ace_xml = '<value>1</value>',
        private string $ace_text = 'plain text',
        private ?int $reference_id = null,
    ) {
    }

    public function getId(): ?int
    {
        return null;
    }

    /**
     * @return array{
     *     date: ?string,
     *     date_time: ?string,
     *     timestamp: ?int,
     *     disabled_timestamp: ?int,
     *     ace_html: string,
     *     ace_javascript: string,
     *     ace_json: string,
     *     ace_php: string,
     *     ace_sql: string,
     *     ace_xml: string,
     *     ace_text: string,
     *     reference_id: ?int
     * }
     */
    public function values(): array
    {
        return [
            'date' => $this->date,
            'date_time' => $this->date_time,
            'timestamp' => $this->timestamp,
            'disabled_timestamp' => $this->disabled_timestamp,
            'ace_html' => $this->ace_html,
            'ace_javascript' => $this->ace_javascript,
            'ace_json' => $this->ace_json,
            'ace_php' => $this->ace_php,
            'ace_sql' => $this->ace_sql,
            'ace_xml' => $this->ace_xml,
            'ace_text' => $this->ace_text,
            'reference_id' => $this->reference_id,
        ];
    }
};

$container = new class implements ContainerInterface {
    public function get(string $id): never
    {
        throw new RuntimeException('Unexpected container lookup: ' . $id);
    }

    public function has(string $id): bool
    {
        return false;
    }
};
$crud = new CRUD($container);
if ($entity->values() !== [
    'date' => '2026-10-20 08:30:45',
    'date_time' => '2026-10-20 08:30:45',
    'timestamp' => 1792474245,
    'disabled_timestamp' => 1792474245,
    'ace_html' => '<strong>html</strong>',
    'ace_javascript' => 'const value = 1;',
    'ace_json' => '{"value":1}',
    'ace_php' => '<?php echo 1;',
    'ace_sql' => 'SELECT 1;',
    'ace_xml' => '<value>1</value>',
    'ace_text' => 'plain text',
    'reference_id' => null,
]) {
    throw new LogicException('Unexpected browser fixture values.');
}

$date = (new CRUDFormWidgetDate('date', false, true))->html($entity, $crud);
$dateTime = (new CRUDFormWidgetDateTime('date_time', true, true))->html($entity, $crud);
$timestamp = (new CRUDFormWidgetTimestamp('timestamp', false, true))->html($entity, $crud);
$disabledTimestamp = (new CRUDFormWidgetTimestamp('disabled_timestamp', false, false, true))->html($entity, $crud);
$aceModes = [
    'ace_html' => CRUDFormWidgetAceTextarea::ACE_MODE_HTML,
    'ace_javascript' => CRUDFormWidgetAceTextarea::ACE_MODE_JAVASCRIPT,
    'ace_json' => CRUDFormWidgetAceTextarea::ACE_MODE_JSON,
    'ace_php' => CRUDFormWidgetAceTextarea::ACE_MODE_PHP,
    'ace_sql' => CRUDFormWidgetAceTextarea::ACE_MODE_SQL,
    'ace_xml' => CRUDFormWidgetAceTextarea::ACE_MODE_XML,
    'ace_text' => CRUDFormWidgetAceTextarea::ACE_MODE_TEXT,
];
$aceWidgets = '';
foreach ($aceModes as $field => $mode) {
    $aceWidgets .= (new CRUDFormWidgetAceTextarea($field, $mode))->html($entity, $crud);
}
$reference = (new CRUDFormWidgetReferenceAjax(
    'reference_id',
    $entity::class,
    static fn (InterfaceEntity $object): string => (string) $object->getId(),
    '/__browser-test/reference-picker',
))->html($entity, $crud);
$request = (new ServerRequestFactory())
    ->createServerRequest('GET', '/__browser-test')
    ->withQueryParams(['companies' => ['2']]);
$multipleAutocomplete = (new CRUDTableFilterMultipleAutocomplete(
    'companies',
    'Companies',
    'companies',
    'id',
    'name',
    '/__browser-test/company-options',
))->getHtml($request);
$popup = CRUDMagnificPopup::button('popup-form', 'btn btn-secondary', 'Open popup')
    . CRUDMagnificPopup::popupHtml('popup-form', '<form id="popup-form-content"><input name="popup_value" value="ready"></form>');
$requiredFormScript = CRUDFormScript::getHtml('required-form');

$dateTime = str_replace('type="datetime-local"', 'type="datetime-local" data-test="date-time"', $dateTime);
$timestamp = str_replace('type="datetime-local"', 'type="datetime-local" data-test="timestamp"', $timestamp);
$disabledTimestamp = str_replace(
    'type="datetime-local"',
    'type="datetime-local" data-test="disabled-timestamp"',
    $disabledTimestamp,
);

header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CRUD browser integration</title>
    <link rel="stylesheet" href="/assets/libraries/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/libraries/tokenize2/tokenize2.min.css">
    <link rel="stylesheet" href="/assets/libraries/medium-editor/css/medium-editor.min.css">
    <link rel="stylesheet" href="/assets/libraries/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/libraries/font-awesome/css/v4-shims.min.css">
    <script src="/assets/libraries/jquery/jquery.min.js"></script>
    <script src="/assets/libraries/jquery-ui/jquery-ui.min.js"></script>
    <script src="/assets/libraries/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/libraries/jquery-validation/jquery.validate.min.js"></script>
    <script src="/assets/libraries/tokenize2/tokenize2.min.js"></script>
    <script src="/assets/libraries/medium-editor/js/medium-editor.min.js"></script>
    <script src="/assets/libraries/blueimp-file-upload/js/jquery.fileupload.js"></script>
    <script src="/assets/libraries/blueimp-file-upload/js/jquery.fileupload-process.js"></script>
    <script src="/assets/libraries/blueimp-file-upload/js/jquery.fileupload-validate.js"></script>
</head>
<body>
<form id="date-widget-form" class="container py-3">
    <?= $date ?>
    <?= $dateTime ?>
    <?= $timestamp ?>
    <?= $disabledTimestamp ?>
</form>
<div class="container pb-3">
    <button id="collapse-toggle" class="btn btn-secondary" type="button" data-bs-toggle="collapse"
            data-bs-target="#bootstrap-collapse" aria-expanded="false" aria-controls="bootstrap-collapse">
        Toggle
    </button>
    <div id="bootstrap-collapse" class="collapse">
        <div class="border rounded p-3">Collapse content</div>
    </div>

    <button id="modal-open" class="btn btn-primary" type="button" data-bs-toggle="modal"
            data-bs-target="#bootstrap-modal">Open modal</button>
    <div id="bootstrap-modal" class="modal fade" tabindex="-1" aria-labelledby="bootstrap-modal-title" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="bootstrap-modal-title" class="modal-title fs-5">Bootstrap modal</h2>
                    <button id="modal-close" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Modal content</div>
            </div>
        </div>
    </div>

    <select id="tokenize-fixture" multiple>
        <option value="1" selected>First</option>
        <option value="2">Second</option>
    </select>
    <div id="medium-editor-fixture">Editable</div>
    <input id="upload-fixture" type="file" name="files[]">
    <i id="font-awesome-fixture" class="fa fa-trash"></i>

    <form id="required-form">
        <input id="required-field" name="required_field" required>
        <button id="required-submit" type="submit">Submit</button>
    </form>
    <?= $requiredFormScript ?>

    <section id="ace-fixtures"><?= $aceWidgets ?></section>
    <section id="reference-fixture"><?= $reference ?></section>
    <form id="autocomplete-form"><?= $multipleAutocomplete ?></form>
    <section id="popup-fixture"><?= $popup ?></section>
</div>
<script>
    window.requiredFormSubmissions = 0;
    $('#required-form').on('submit', function (event) {
        event.preventDefault();
        window.requiredFormSubmissions += 1;
    });
    window.autocompleteFormSubmissions = 0;
    $('#autocomplete-form').on('submit', function (event) {
        event.preventDefault();
        window.autocompleteFormSubmissions += 1;
    });
    window.bootstrapModalShown = false;
    $('#bootstrap-modal').on('shown.bs.modal', function () {
        window.bootstrapModalShown = true;
    });
    $('#tokenize-fixture').tokenize2();
    new MediumEditor('#medium-editor-fixture');
    $('#upload-fixture').fileupload({dataType: 'json'});
</script>
</body>
</html>
