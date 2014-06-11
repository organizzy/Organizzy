<?php
/* @var $this DepartmentController */
/* @var $model Department */

$this->layoutSingle($this->createUrl('/organization/view', ['id' => $model->organization_id]));
$this->pageTitle = _t('Add Department');
?>

<?php //echo $model->organization->name ?>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>