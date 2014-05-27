<?php
/* @var $this SiteController */
/* @var $error array */

$this->pageTitle = 'Error';
$this->layout = '//layouts/single'
?>
<div class="content-padded">
    <h2>Error <?php echo $code; ?></h2>

    <div class="error">
        <?php echo CHtml::encode($message); ?>
    </div>
    <?php var_dump($_SERVER); ?>
</div>
