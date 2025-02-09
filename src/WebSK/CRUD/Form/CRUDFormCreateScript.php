<?php

namespace WebSK\CRUD\Form;

use WebSK\CRUD\CRUDPreloader;

/**
 * Class CRUDFormCreateScript
 * @package WebSK\CRUD\Form
 */
class CRUDFormCreateScript
{
    /**
     * @param string $form_id
     * @param string $table_class
     */
    public static function render(string $form_id, string $table_class): void
    {
        static $include_script;

        if (!isset($include_script)) {
            $include_script = false;

            echo CRUDPreloader::preloader();
            ?>
            <script>
                var CRUD = CRUD || {};

                CRUD.CreateForm = CRUD.CreateForm || {

                    init: function (form_elem, table_elem) {
                        var $form = $(form_elem);

                        $form.on('submit', function (e) {
                            e.preventDefault();
                            var url = $form.attr('action');
                            var data = $form.serializeArray();

                            CRUD.CreateForm.requestAjax(table_elem, url, data);
                        });
                    },

                    requestAjax: function (table_elem, query, data) {

                        CRUDPage.preloader.show();

                        $.ajax({
                            type: "POST",
                            url: query,
                            data: data
                        }).success(function (received_html) {
                            CRUDPage.preloader.hide();

                            var $box = $('<div>', {html: received_html});
                            $(table_elem).html($box.find(table_elem).html());
                        }).fail(function () {
                            CRUDPage.preloader.hide();
                        });
                    }
                };
            </script>
            <?php
        }
        ?>
        <script>
            CRUD.CreateForm.init('#<?= $form_id ?>', '.<?= $table_class ?>');
        </script>
        <?php
    }

    /**
     * @param string $form_id
     * @param string $table_class
     * @return string
     */
    public static function getHtml(string $form_id, string $table_class): string
    {
        ob_start();
        self::render($form_id, $table_class);
        return ob_get_clean();
    }
}