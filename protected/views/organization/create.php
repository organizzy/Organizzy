<?php
/* @var $this OrganizationController */
/* @var $model Organization */

$this->layoutSingle(['index']);
$this->pageTitle = O::t('organizzy', 'Create Organization');
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>