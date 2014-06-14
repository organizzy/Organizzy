<?php
/* @var $this EventController */
/* @var $model Event */

$this->layoutSingle(array('view', 'id' => $model->id));
$this->pageTitle = _t('Edit Event')
?>
<div class="content-padded">
<?php $this->renderPartial('_form', array('model'=>$model, 'recurrence' => null)); ?>
</div>