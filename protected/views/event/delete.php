<?php
/* @var $this Controller */
/* @var $model Event */

$this->layoutSingle(array('view', 'id' => $model->id));
$this->pageTitle = 'Delete Event';
?>
<div class="content-padded">
    <p><?php printf(O::t('organizzy', 'Are you sure you want to delete event %s?'), '<b>' . $model->title . '</b>') ?></p>
    <form method="post">
        <?php echo CHtml::hiddenField('confirm', 1) ?>
        <?php echo CHtml::submitButton('Delete', array('class' => 'btn btn-block btn-primary')) ?>
        <?php echo CHtml::link('Cancel', array('view', 'id' => $model->id), array('class' => 'btn btn-block btn-link')) ?>
    </form>
</div>