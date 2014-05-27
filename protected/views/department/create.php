<?php
/* @var $this DepartmentController */
/* @var $model Department */

$this->layoutSingle($this->createUrl('/organization/view', ['id' => $model->organization_id]));
$this->pageTitle = O::t('organizzy', 'Create Department');
?>

<?php //echo $model->organization->name ?>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>