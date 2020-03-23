<?php

namespace WebSK\CRUD\Form\Widgets;

use Closure;
use OLOG\Operations;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDCompiler;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Form\CRUDForm;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;
use WebSK\Utils\Sanitize;

/**
 * Class CRUDFormWidgetUpload
 * @package WebSK\CRUD\Form\Widgets
 */
class CRUDFormWidgetUpload implements InterfaceCRUDFormWidget
{
    const DEFAULT_MAX_FILE_SIZE = 52428800;

    const FILE_TYPE_IMAGE = 'image';

    /** @var string */
    protected $field_name;

    /** @var string */
    protected $storage;

    /** @var string */
    protected $target_folder;

    /** @var string|Closure */
    protected $url;

    /** @var $save_as */
    protected $save_as;

    /** @var string */
    protected $file_type = '';

    /** @var string */
    protected $form_action_url = '';

    protected $max_file_size = self::DEFAULT_MAX_FILE_SIZE;

    /** @var array */
    protected $allowed_extensions = [];

    /**
     * CRUDFormWidgetUpload constructor.
     * @param string $field_name
     * @param string $storage
     * @param string $target_folder
     * @param string|Closure $url
     * @param string $save_as
     * @param string $file_type
     * @param array $allowed_extensions
     * @param int $max_file_size
     * @param string $form_action_url
     */
    public function __construct(
        string $field_name,
        string $storage,
        string $target_folder,
        $url,
        string $save_as = '',
        string $file_type = '',
        array $allowed_extensions = [],
        int $max_file_size = self::DEFAULT_MAX_FILE_SIZE,
        string $form_action_url = ''
    ) {
        $this->setFieldName($field_name);
        $this->setStorage($storage);
        $this->setTargetFolder($target_folder);
        $this->setUrl($url);
        $this->setSaveAs($save_as);
        $this->setFileType($file_type);
        $this->setAllowedExtensions($allowed_extensions);
        $this->setMaxFileSize($max_file_size);
        $this->setFormActionUrl($form_action_url);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        static $crud_form_widget_blueimp_file_upload_include_script;

        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        $element_id = 'fileupload_' . Sanitize::sanitizeAttrValue($field_name) . '_' . rand(1, 999999);

        $file_url = CRUDCompiler::fieldValueOrCallableResult($this->getUrl(), $obj);

        $html = '';

        if (!isset($crud_form_widget_blueimp_file_upload_include_script)) {
            $cdn_blueimp_file_upload_path = 'https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/10.7.0';

            $html .= '<link rel="stylesheet" href="' . $cdn_blueimp_file_upload_path . '/css/jquery.fileupload.css">' .
                '<script src="' . $cdn_blueimp_file_upload_path . '/js/jquery.fileupload.js"></script>' .
                '<script src="' . $cdn_blueimp_file_upload_path . '/js/jquery.fileupload-process.js"></script>' .
                '<script src="' . $cdn_blueimp_file_upload_path . '/js/jquery.fileupload-validate.js"></script>';

            $crud_form_widget_blueimp_file_upload_include_script = false;
        }

        $accept_file_types = 'undefined';
        $allowed_extensions = $this->getAllowedExtensions();
        if ($allowed_extensions) {
            $accept_file_types = '/(\.|\/)(' . implode('|', $allowed_extensions) . ')$/i';
        }

        $max_file_size = $this->getMaxFileSize();

        $fileinput_button_id = 'fileinput-button-' .  $element_id;
        $files_area_id = 'files-' . $element_id;

        ob_start();
        ?>
        <span class="btn btn-success fileinput-button" id="<?php echo $fileinput_button_id; ?>">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Выберите файл...</span>
            <input id="<?php echo $element_id; ?>" type="file" name="file_<?php echo $field_name; ?>">
        </span>

        <div id="<?php echo $files_area_id; ?>"></div>

        <script>
            $(function () {
                function viewFile(file_url, file_name) {
                    if (!file_url) {
                        return;
                    }

                    $('#<?php echo $fileinput_button_id; ?>').hide();

                    var html = '<a href="' + file_url +'" target="_blank">';
                    <?php
                    $file_type = $this->getFileType();

                    if ($file_type == self::FILE_TYPE_IMAGE) {
                    ?>
                    html += '<img src="' + file_url +'" class="img-responsive img-thumbnail" style="max-width: 200px">';
                    <?php
                    } else {
                    ?>
                    html += '<p>' + file_name + '</p>';
                    <?php
                    }
                    ?>
                    html += '</a>';
                    html += '<p><button class="btn btn-danger" title="Удалить">Удалить</button></p>';

                    $('#<?php echo $files_area_id; ?>').html(html);
                }

                let url = '<?php echo $this->getFormActionUrl(); ?>';

                viewFile('<?php echo $file_url; ?>', '<?php echo $field_value; ?>');

                $('#<?php echo $files_area_id; ?>').on('click', 'button', function (e) {
                    e.preventDefault();

                    let link = $(this);

                    $.ajax({
                        dataType: 'json',
                        data: {
                            '<?php echo Operations::FIELD_NAME_OPERATION_CODE; ?>': '<?php echo CRUDForm::OPERATION_DELETE_FILE; ?>',
                            '<?php echo CRUDForm::FIELD_STORAGE; ?>': '<?php echo Sanitize::sanitizeAttrValue($this->getStorage()); ?>',
                            '<?php echo CRUDForm::FIELD_TARGET_FOLDER; ?>': '<?php echo addslashes(Sanitize::sanitizeAttrValue($this->getTargetFolder())); ?>',
                            '<?php echo CRUDForm::FIELD_FIELD_NAME; ?>': '<?php echo Sanitize::sanitizeAttrValue($this->getFieldName()); ?>',
                            '<?php echo CRUDForm::FIELD_SAVE_AS; ?>': '<?php echo Sanitize::sanitizeAttrValue($this->getSaveAs()); ?>'
                        },
                        url: url,
                        type: 'POST'
                    }).success(function (data) {
                        if (data.error) {
                            var error_msg = $('<span class="text-danger"/>').text(data.error);
                            $('#<?php echo $files_area_id; ?>').append(error_msg);
                            return;
                        }

                        $('#<?php echo $fileinput_button_id; ?>').show();

                        link.closest('p').remove();
                        $('#<?php echo $files_area_id; ?>').html('');
                    }).error(
                        function (xhr, status, error) {
                            var error_msg = $('<span class="text-danger"/>').text(error);
                            $('#<?php echo $files_area_id; ?>').append(error_msg);
                        }
                    );
                });

                $('#<?php echo $element_id; ?>').fileupload({
                    url: url,
                    dataType: 'json',
                    formData: [
                        {name: '<?php echo Operations::FIELD_NAME_OPERATION_CODE; ?>', value: '<?php echo CRUDForm::OPERATION_UPLOAD_FILE; ?>'},
                        {name: '<?php echo CRUDForm::FIELD_STORAGE; ?>', value: '<?php echo Sanitize::sanitizeAttrValue($this->getStorage()); ?>'},
                        {name: '<?php echo CRUDForm::FIELD_TARGET_FOLDER; ?>', value: '<?php echo addslashes(Sanitize::sanitizeAttrValue($this->getTargetFolder())); ?>'},
                        {name: '<?php echo CRUDForm::FIELD_FIELD_NAME; ?>', value: '<?php echo Sanitize::sanitizeAttrValue($this->getFieldName()); ?>'},
                        {name: '<?php echo CRUDForm::FIELD_SAVE_AS; ?>', value: '<?php echo Sanitize::sanitizeAttrValue($this->getSaveAs()); ?>'}
                    ],
                    autoUpload: true,
                    acceptFileTypes: <?php echo $accept_file_types; ?>,
                    maxFileSize: <?php echo $max_file_size; ?>,
                    previewThumbnail: true,
                    maxNumberOfFiles: 1,
                    messages: {
                        maxNumberOfFiles: 'Превышено максимальное количество файлов, загружаемое за один раз',
                        acceptFileTypes: 'Неверный тип файла',
                        maxFileSize: 'Размер файла слишком большой',
                        minFileSize: 'Размер файла слишком маленький'
                    }
                }).on('fileuploadadd', function (e, data) {
                    data.context = $('<div/>').appendTo('#<?php echo $files_area_id; ?>');
                    $.each(data.files, function (index, file) {
                        var node = $('<p/>')
                            .append($('<span/>').text(file.name));
                        node.appendTo(data.context);
                    });
                }).on('fileuploadprocessalways', function (e, data) {
                    $(".btn-primary").prop("disabled", false);

                    var index = data.index,
                        file = data.files[index],
                        node = $(data.context.children()[index]);
                    if (file.error) {
                        node
                            .append('<br>')
                            .append($('<span class="text-danger"/>').text(file.error));
                    }
                }).on('fileuploaddone', function (e, data) {
                    $.each(data.result.files, function (index, file) {
                        if (file.name) {
                            $(data.context.children()[index])
                                .append(' <div class="text-success">Файл  загружен</div>');

                            $('#<?php echo $fileinput_button_id; ?>').hide();

                        } else if (file.error) {
                            var error = $('<span class="text-danger"/>').text(file.error);
                            $(data.context.children()[index])
                                .append('<br>')
                                .append(error);
                        }
                    });

                    event.preventDefault();
                    $(".btn-primary").prop("disabled", false);
                }).on('fileuploadfail', function (e, data) {
                    $.each(data.files, function (index) {
                        var error = $('<span class="text-danger"/>').text('Файл не удалось загрузить.');
                        $(data.context.children()[index])
                            .append('<br>')
                            .append(error);
                    });

                    event.preventDefault();
                    $(".btn-primary").prop("disabled", false);
                }).on('fileuploadstart', function (e) {
                    $(".btn-primary").prop("disabled", true);
                }).prop('disabled', !$.support.fileInput)
                    .parent().addClass($.support.fileInput ? undefined : 'disabled');
            });
        </script>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->field_name;
    }

    /**
     * @param string $field_name
     */
    public function setFieldName(string $field_name): void
    {
        $this->field_name = $field_name;
    }

    /**
     * @return string
     */
    public function getStorage(): string
    {
        return $this->storage;
    }

    /**
     * @param string $storage
     */
    public function setStorage(string $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * @return string
     */
    public function getTargetFolder(): string
    {
        return $this->target_folder;
    }

    /**
     * @param string $target_folder
     */
    public function setTargetFolder(string $target_folder): void
    {
        $this->target_folder = $target_folder;
    }

    /**
     * @return Closure|string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param Closure|string $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getFileType(): string
    {
        return $this->file_type;
    }

    /**
     * @param string $file_type
     */
    public function setFileType(string $file_type): void
    {
        $this->file_type = $file_type;
    }

    /**
     * @return string
     */
    public function getFormActionUrl(): string
    {
        return $this->form_action_url;
    }

    /**
     * @param string $form_action_url
     */
    public function setFormActionUrl(string $form_action_url): void
    {
        $this->form_action_url = $form_action_url;
    }

    /**
     * @return array
     */
    public function getAllowedExtensions(): array
    {
        return $this->allowed_extensions;
    }

    /**
     * @param array $allowed_extensions
     */
    public function setAllowedExtensions(array $allowed_extensions): void
    {
        $this->allowed_extensions = $allowed_extensions;
    }

    /**
     * @return int
     */
    public function getMaxFileSize(): int
    {
        return $this->max_file_size;
    }

    /**
     * @param int $max_file_size
     */
    public function setMaxFileSize(int $max_file_size): void
    {
        $this->max_file_size = $max_file_size;
    }

    /**
     * @return mixed
     */
    public function getSaveAs()
    {
        return $this->save_as;
    }

    /**
     * @param mixed $save_as
     */
    public function setSaveAs($save_as): void
    {
        $this->save_as = $save_as;
    }
}
