<?php
/* @var $this OrganizationController */
/* @var $model Organization */
/* @var $form CActiveForm */
?>

<div class="form content-padded">

<?php $form=$this->beginWidget('CActiveForm'); ?>

	<p class="note"><?php _p('Fields with {*} are required.', ['{*}' => '<span class="required">*</span>']); ?></p>

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
        echo $form->labelEx($role, 'position', ['label' => _t('My Role')]);
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
		<?php echo CHtml::submitButton($model->isNewRecord ? _t('Create') : _t('Save'), array('class' => 'btn btn-block btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->