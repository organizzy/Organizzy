<?php
/* @var $this OrganizationController */
/* @var $model Organization */

$this->layoutSingle(['index']);
$this->pageTitle = _t('Create Organization');
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>