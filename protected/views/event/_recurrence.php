<?php
/* @var $this EventController */
/* @var $model Event */

$this->layoutSingle(array('view', 'id' => $model->id));
$this->pageTitle = 'Edit Event'
?>
<div class="content-padded">
    <?php $this->renderPartial('_form_recurrence', array('model'=>$model)); ?>
</div>