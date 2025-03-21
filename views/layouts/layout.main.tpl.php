<?php
/**
 * @var LayoutDTO $layout_dto
 */

use WebSK\Utils\Assert;
use WebSK\Utils\Messages;
use WebSK\Config\ConfWrapper;
use WebSK\Utils\Sanitize;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Utils\Url;
?>
<!DOCTYPE html>
<html lang="ru">
<head xmlns:og="http://ogp.me/ns#">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title><?= $layout_dto->getTitle() ?></title>

    <!-- jQuery -->
    <script src="/assets/libraries/jquery/jquery.min.js"></script>
    <link href="/assets/libraries/jquery-ui/themes/base/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/assets/libraries/jquery-ui/jquery-ui.min.js"></script>

    <!-- Bootstrap -->
    <link href="/assets/libraries/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <script src="/assets/libraries/bootstrap/js/bootstrap.min.js"></script>

    <link href="/assets/libraries/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

    <script type="text/javascript" src="/assets/libraries/jquery-validation/jquery.validate.min.js"></script>

    <script type="text/javascript" src="/assets/libraries/fancybox/jquery.fancybox.min.js"></script>
    <link href="/assets/libraries/fancybox/jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>

    <meta name="keywords" content="<?php echo $layout_dto->getKeywords() ?>"/>
    <meta name="description" content="<?php echo $layout_dto->getDescription() ?>"/>
    <style>
        body {margin:0; padding:0; font-family: Arial; background-color: #fff;}
        #html {width: 90%; margin: auto; font-size: 12px; line-height: 18px; margin-bottom: 30px;}
        #content {width: 98%; margin-bottom: 30px; font-size: 14px;}
        #header {margin: 20px 0;}
        #footer {margin-top: 20px; background-color: #C0C0C0; color: #ffffff; padding: 10px}
    </style>
</head>
<body>

<div id="html">
    <div id="header" class="row">
        <div style="font-size: 22px;"><?php echo  ConfWrapper::value('site_title')?></div>
    </div>

    <div>
        <div class="row">
            <div id="content">
                <ol class="breadcrumb">
                    <?php
                    foreach ($layout_dto->getBreadcrumbsDtoArr() as $breadcrumb_item_dto) {
                        Assert::assert($breadcrumb_item_dto instanceof BreadcrumbItemDTO);
                        if (!$breadcrumb_item_dto->getUrl()) {
                            echo '<li class="active">' . $breadcrumb_item_dto->getName() . '</li>';
                            continue;
                        }

                        echo '<li><a href="' . Sanitize::sanitizeUrl($breadcrumb_item_dto->getUrl()) . '">' . Sanitize::sanitizeTagContent($breadcrumb_item_dto->getName()) . '</a></li>';
                    }
                    ?>
                </ol>
                <?php
                $current_url_no_query = Url::getUriNoQueryString();

                if ($current_url_no_query != '/') {
                    ?>
                    <h1><?php echo $layout_dto->getTitle(); ?></h1>
                    <hr class="hidden-xs hidden-sm">
                    <?php
                }
                ?>

                <?php
                echo Messages::renderMessages();
                ?>

                <?php echo $layout_dto->getContentHtml(); ?>
            </div>
        </div>
    </div>

    <div id="footer" class="row">
        <div>
            &copy; <?php echo ConfWrapper::value('site_name'); ?>, <?php echo date('Y'); ?>
        </div>
    </div>
</div>

</body>
</html>