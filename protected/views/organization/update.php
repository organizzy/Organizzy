<?php
/* @var $this OrganizationController */
/* @var $model Organization */

$this->layoutSingle();
$this->pageTitle = 'Edit';
$this->backUrl = $this->createUrl('view', array('id' => $model->id));
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>