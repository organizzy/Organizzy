<?php
/* @var $this DepartmentController */
/* @var $model Department */

$this->layoutSingle($this->createUrl('view', ['id' => $model->id]));
$this->pageTitle = _t('Edit Department');
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>