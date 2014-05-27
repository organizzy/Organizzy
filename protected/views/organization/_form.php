<?php
/* @var $this OrganizationController */
/* @var $model Organization */
/* @var $form CActiveForm */
?>

<div class="form content-padded">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'organization-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows' => 2, 'maxlength'=>1024)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

    <?php if ($model->scenario == 'create'): ?>
        <?php
        $role = new Role();
        echo $form->labelEx($role, 'position', ['label' => O::t('organizzy', 'My Role')]);
        $this->renderPartial('//role/_position', ['model' => $role]);
        ?>
    <?php else: ?>
        <div class="row">
            <?php echo $form->labelEx($model,'info'); ?>
            <?php echo $form->textArea($model,'info',array('rows' => 5, 'maxlength'=>1024)); ?>
            <?php echo $form->error($model,'info'); ?>
        </div>
    <?php endif ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn btn-block btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->