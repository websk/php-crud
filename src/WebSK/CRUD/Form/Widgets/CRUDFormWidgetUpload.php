<?php

namespace WebSK\CRUD\Form\Widgets;

use OLOG\Operations;
use WebSK\CRUD\CRUD;
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
    const MAX_FILE_SIZE = 20971520;

    const FILE_TYPE_IMAGE = 'image';

    const FIELD_TARGET_FOLDER = 'target_folder';
    const FIELD_ROOT_FOLDER = 'root_folder';

    /** @var string */
    protected $field_name;

    /** @var string */
    protected $target_folder;

    /** @var string */
    protected $root_folder;

    /** @var string */
    protected $form_action_url = '';

    /** @var string */
    protected $file_type;

    public function __construct(
        string $field_name,
        string $file_type,
        string $target_folder,
        string $root_folder = '',
        string $form_action_url = ''
    ) {
        $this->setFieldName($field_name);
        $this->setFileType($file_type);
        $this->setTargetFolder($target_folder);
        $this->setRootFolder($root_folder);
        $this->setFormActionUrl($form_action_url);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        static $crud_form_widget_blueimp_file_upload_include_script;

        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        $element_id = 'fileupload_' . Sanitize::sanitizeAttrValue($field_name) . '_' . rand(1, 999999);

        $html = '';

        if (!isset($crud_form_widget_blueimp_file_upload_include_script)) {
            $cdn_blueimp_file_upload_path = 'https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/10.7.0';

            $html .= '<link rel="stylesheet" href="' . $cdn_blueimp_file_upload_path . '/css/jquery.fileupload.css">' .
            '<script src="' . $cdn_blueimp_file_upload_path . '/js/jquery.fileupload.js"></script>' .
            '<script src="' . $cdn_blueimp_file_upload_path . '/js/jquery.fileupload-process.js"></script>' .
            '<script src="' . $cdn_blueimp_file_upload_path . '/js/jquery.fileupload-validate.js"></script>';

            $crud_form_widget_blueimp_file_upload_include_script = false;
        }

        $file_type = $this->getFileType();

        $accept_file_types = '@';
        switch($file_type) {
            case self::FILE_TYPE_IMAGE:
                $accept_file_types = '/(\.|\/)(gif|jpe?g|png)$/i';
        }

        ob_start();
        ?>
        <span class="btn btn-success fileinput-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Выберите файл...</span>
            <input id="<?php echo $element_id; ?>" type="file" name="load_file">
        </span>

        <div id="files_<?php echo $element_id; ?>"></div>

        <script>
            function viewFile(file_url) {
                if (!file_url) {
                    return;
                }

                var html = '<img src="' + file_url +'" class="img-responsive img-thumbnail">';

                html += '<p><button class="btn btn-danger" title="Удалить">Удалить</button></p>';

                $('#files_<?php echo $element_id; ?>').html(html);
            }

            $(function () {
                var url = '<?php echo $this->getFormActionUrl(); ?>';

                var file_url = '<?php echo $field_value ? '/files/'. $this->getTargetFolder() . '/' . $field_value : ''; ?>';
                viewFile(file_url);

                $('#files_<?php echo $element_id; ?>').on('click', 'button', function (e) {
                    e.preventDefault();

                    var link = $(this);

                    var req = $.ajax({
                        dataType: 'json',
                        data: {
                            '<?php echo Operations::FIELD_NAME_OPERATION_CODE; ?>': '<?php echo CRUDForm::OPERATION_DELETE_FILE; ?>',
                            '<?php echo self::FIELD_TARGET_FOLDER; ?>e': '<?php echo addslashes(Sanitize::sanitizeAttrValue($this->getTargetFolder())); ?>',
                            '<?php echo self::FIELD_ROOT_FOLDER; ?>': '<?php echo addslashes(Sanitize::sanitizeAttrValue($this->getRootFolder())); ?>',
                            '<?php echo CRUDForm::FIELD_FIELD_NAME; ?>': '<?php echo Sanitize::sanitizeAttrValue($this->getFieldName()); ?>'
                        },
                        url: url,
                        type: 'POST'
                    }).success(function (data) {
                        if (data.error) {
                            var error_msg = $('<span class="text-danger"/>').text(data.error);
                            $('#files_<?php echo $element_id; ?>').append(error_msg);
                            return;
                        }

                        link.closest('p').remove();
                        $('#files_<?php echo $element_id; ?>').html('');
                    }).error(
                        function (xhr, status, error) {
                            var error_msg = $('<span class="text-danger"/>').text(error);
                            $('#files_<?php echo $element_id; ?>').append(error_msg);
                        }
                    );
                });

                $('#<?php echo $element_id; ?>').fileupload({
                    url: url,
                    dataType: 'json',
                    formData: [
                        {name: '<?php echo Operations::FIELD_NAME_OPERATION_CODE; ?>', value: '<?php echo CRUDForm::OPERATION_UPLOAD_FILE; ?>'},
                        {name: '<?php echo self::FIELD_TARGET_FOLDER; ?>', value: '<?php echo addslashes(Sanitize::sanitizeAttrValue($this->getTargetFolder())); ?>'},
                        {name: '<?php echo self::FIELD_ROOT_FOLDER; ?>', value: '<?php echo addslashes(Sanitize::sanitizeAttrValue($this->getRootFolder())); ?>'},
                        {name: '<?php echo CRUDForm::FIELD_FIELD_NAME; ?>', value: '<?php echo Sanitize::sanitizeAttrValue($this->getFieldName()); ?>'}
                    ],
                    autoUpload: true,
                    acceptFileTypes: <?php echo $accept_file_types; ?>,
                    maxFileSize: <?php echo self::MAX_FILE_SIZE; ?>,
                    previewThumbnail: true,
                    maxNumberOfFiles: 1,
                    messages: {
                        maxNumberOfFiles: 'Превышено максимальное количество файлов, загружаемое за один раз',
                        acceptFileTypes: 'Неверный тип файла',
                        maxFileSize: 'Размер файла слишком большой',
                        minFileSize: 'Размер файла слишком маленький'
                    }
                }).on('fileuploadadd', function (e, data) {
                    data.context = $('<div/>').appendTo('#files_<?php echo $element_id; ?>');
                    $.each(data.files, function (index, file) {
                        var node = $('<p/>')
                            .append($('<span/>').text(file.name));
                        node.appendTo(data.context);
                    });
                }).on('fileuploadprocessalways', function (e, data) {
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
                        if (file.url) {
                            $(data.context.children()[index])
                                .append(' <div class="text-success">Файл  загружен</div>');

                            viewFile(file.url);
                        } else if (file.error) {
                            var error = $('<span class="text-danger"/>').text(file.error);
                            $(data.context.children()[index])
                                .append('<br>')
                                .append(error);
                        }
                    });
                }).on('fileuploadfail', function (e, data) {
                    $.each(data.files, function (index) {
                        var error = $('<span class="text-danger"/>').text('Файл не удалось загрузить.');
                        $(data.context.children()[index])
                            .append('<br>')
                            .append(error);
                    });
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
    public function getRootFolder(): string
    {
        return $this->root_folder;
    }

    /**
     * @param string $root_folder
     */
    public function setRootFolder(string $root_folder): void
    {
        $this->root_folder = $root_folder;
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
}
