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
    /** @var string */
    protected $field_name;

    /** @var string */
    protected $target_folder;

    /** @var string */
    protected $root_folder;

    /** @var string */
    protected $form_action_url = '';

    public function __construct(
        string $field_name,
        string $target_folder,
        string $root_folder = '',
        string $form_action_url = ''
    ) {
        $this->setFieldName($field_name);
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

        $html = '';

        if (!isset($crud_form_widget_blueimp_file_upload_include_script)) {
            $html .= '<link rel="stylesheet" href="/assets/libraries/blueimp-file-upload/css/jquery.fileupload.css">' .
            '<script src="/assets/libraries/blueimp-file-upload/js/jquery.fileupload.js"></script>' .
            '<script src="/assets/libraries/blueimp-file-upload/js/jquery.fileupload-process.js"></script>' .
            '<script src="/assets/libraries/blueimp-file-upload/js/jquery.fileupload-validate.js"></script>';

            $crud_form_widget_blueimp_file_upload_include_script = false;
        }

        ob_start();
        ?>
        <span class="btn btn-success fileinput-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Выберите файл...</span>
            <input id="load_file" type="file" name="load_file">
        </span>

        <div id="image"></div>

        <script>
            $(function () {
                var url = '<?php echo $this->getFormActionUrl(); ?>';

                $('#load_file').fileupload({
                    url: url,
                    dataType: 'json',
                    formData: [
                        {name: '<?php echo Operations::FIELD_NAME_OPERATION_CODE; ?>', value: '<?php echo CRUDForm::OPERATION_UPLOAD_FILE; ?>'},
                        {name: 'target_folder', value: '<?php echo addslashes(Sanitize::sanitizeAttrValue($this->getTargetFolder())); ?>'},
                        {name: 'root_folder', value: '<?php echo addslashes(Sanitize::sanitizeAttrValue($this->getRootFolder())); ?>'}
                    ],
                    autoUpload: true,
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                    maxFileSize: 20971520,
                    previewThumbnail: false,
                    maxNumberOfFiles: 1,
                    messages: {
                        maxNumberOfFiles: 'Превышено максимальное количество файлов, загружаемое за один раз',
                        acceptFileTypes: 'Этот файл не изображение',
                        maxFileSize: 'Размер файла слишком большой',
                        minFileSize: 'Размер файла слишком маленький'
                    }
                }).on('fileuploadadd', function (e, data) {
                    data.context = $('<div/>').appendTo('#files');
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
                            $('#image').html('<img src="' + file.url +'" class="img-responsive img-thumbnail">');
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
}
