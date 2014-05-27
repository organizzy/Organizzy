<?php
/* @var $this DepartmentController */
/* @var $model Department */

$this->layoutSingle($this->createUrl('view', ['id' => $model->id]));
$this->pageTitle = O::t('organizzy', 'Edit Department');
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>