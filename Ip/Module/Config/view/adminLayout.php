<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->doctypeDeclaration(); ?>

<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    $site->addCss(\Ip\Config::coreModuleUrl('Config/public/admin.css'));
    echo $site->generateHead();
    ?>
</head>
<body>
    <?php echo empty($content) ? '' : $content ?>
<?php
echo $site->generateJavascript();
?>
</body>
</html>
