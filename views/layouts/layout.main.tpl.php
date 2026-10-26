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
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= Sanitize::sanitizeTagContent($layout_dto->getTitle()) ?></title>

    <!-- jQuery -->
    <script src="/assets/libraries/jquery/jquery.min.js"></script>
    <link href="/assets/libraries/jquery-ui/themes/base/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/assets/libraries/jquery-ui/jquery-ui.min.js"></script>

    <!-- Bootstrap -->
    <link href="/assets/libraries/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <script src="/assets/libraries/bootstrap/js/bootstrap.bundle.min.js"></script>

    <link href="/assets/libraries/font-awesome/css/all.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/libraries/font-awesome/css/v4-shims.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/libraries/crud/crud.css" rel="stylesheet" type="text/css"/>

    <script type="text/javascript" src="/assets/libraries/jquery-validation/jquery.validate.min.js"></script>

    <meta name="keywords" content="<?= Sanitize::sanitizeAttrValue($layout_dto->getKeywords()) ?>">
    <meta name="description" content="<?= Sanitize::sanitizeAttrValue($layout_dto->getDescription()) ?>">
</head>
<body>

<div id="html" class="container-fluid px-4 pb-4">
    <div id="header" class="row my-4">
        <div class="col-12 fs-4"><?= Sanitize::sanitizeTagContent((string) ConfWrapper::value('site_title')) ?></div>
    </div>

    <div>
        <div class="row">
            <div id="content" class="col-12 pb-4">
                <ol class="breadcrumb">
                    <?php
                    foreach ($layout_dto->getBreadcrumbsDtoArr() as $breadcrumb_item_dto) {
                        Assert::assert($breadcrumb_item_dto instanceof BreadcrumbItemDTO);
                        if (!$breadcrumb_item_dto->getUrl()) {
                            echo '<li class="breadcrumb-item active" aria-current="page">' . Sanitize::sanitizeTagContent($breadcrumb_item_dto->getName()) . '</li>';
                            continue;
                        }

                        echo '<li class="breadcrumb-item"><a href="' . Sanitize::sanitizeUrl($breadcrumb_item_dto->getUrl()) . '">' . Sanitize::sanitizeTagContent($breadcrumb_item_dto->getName()) . '</a></li>';
                    }
                    ?>
                </ol>
                <?php
                $current_url_no_query = Url::getUriNoQueryString();

                if ($current_url_no_query !== '/') {
                    ?>
                    <h1><?= Sanitize::sanitizeTagContent($layout_dto->getTitle()) ?></h1>
                    <hr class="d-none d-md-block">
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

    <div id="footer" class="row mt-4 bg-secondary text-white p-2">
        <div class="col-12">
            &copy; <?= Sanitize::sanitizeTagContent((string) ConfWrapper::value('site_name')) ?>, <?= date('Y') ?>
        </div>
    </div>
</div>

</body>
</html>
