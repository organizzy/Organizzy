<?php

/* @var $this Controller */
/* @var string $content */
if (isset($_GET['return'])) {
    $this->backUrl = $_GET['return'];
}
?>
<?php $this->beginContent('//layouts/main'); ?>
<header class="bar bar-nav">
    <a class="btn-back pull-left" href="<?php echo is_array($this->backUrl) ? $this->createUrl($this->backUrl) : $this->backUrl ?>">

        <h1 class="title"><i class="icon icon-left-nav"></i><?php echo $this->pageTitle ?></h1>
    </a>
    <?php if ($this->menu) { ?>
        <a class="bar-btn pull-right" href="#" id="tab-item-4"><i class="icon fa fa-ellipsis-v"></i></a>
    <?php } elseif ($this->editButton) {  ?>
        <a class="icon fa bar-btn <?php echo isset($this->editButton['icon']) ? $this->editButton['icon'] : 'fa-edit' ?> pull-right" href="<?php echo $this->editButton['url'] ?>"></a>
    <?php } ?>

</header>
<?php
if ($this->menu) {
    $this->renderPartial('//layouts/_menu', array('menu' => $this->menu));
}
?>
<div class="content" id="content-<?php echo $this->getPageId() ?>">
    <?php echo $content ?>
</div><!-- #content -->
<?php $this->endContent() ?>