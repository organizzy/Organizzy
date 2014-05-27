<?php
/* @var $this Controller */
/* @var $model Organization */
/* @var $lastAdmin boolean */

$this->layoutSingle();
$this->pageTitle = 'Leave Organization';
$this->backUrl = $this->createUrl('view', array('id' => $model->id));
?>
<?php if ($lastAdmin): ?>
    <div class="content-padded">
        <p>You can't leave this organization because you are the last admin of this organization</p>
    </div>
<?php else: ?>
<div class="content-padded">
    <p><?php printf(O::t('organizzy', 'Are you sure you want to leave %s'), '<b>' . $model->name . '</b>') ?></p>
    <form method="post">
        <?php echo CHtml::hiddenField('leave', 1) ?>
        <?php echo CHtml::submitButton('Leave', array('class' => 'btn btn-block btn-primary')) ?>
        <?php echo CHtml::link('Cancel', array('view', 'id' => $model->id), array('class' => 'btn btn-block btn-link')) ?>
    </form>
</div>
<?php endif ?>