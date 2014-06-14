<?php
/* @var $this Controller */
/* @var $model Organization */
/* @var $lastAdmin boolean */

$this->layoutSingle();
$this->pageTitle = 'Delete Organization';
$this->backUrl = $this->createUrl('view', array('id' => $model->id));
?>

<div class="content-padded">
    <p><?php _p('Are you sure you want to delete organization {organization}?', ['{organization}' => '<b>' . $model->name . '</b>']) ?></p>
    <form method="post">
        <?php echo CHtml::hiddenField('confirm', 1) ?>
        <?php echo CHtml::submitButton('Delete', array('class' => 'btn btn-block btn-primary')) ?>
        <?php echo CHtml::link('Cancel', array('view', 'id' => $model->id), array('class' => 'btn btn-block btn-link')) ?>
    </form>
</div>