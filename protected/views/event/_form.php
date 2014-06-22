<?php
/* @var EventController $this  */
/* @var $model Event */
/* @var $recurrence EventRecurrence */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm'); ?>
    <p class="note">
        <?php _p('Fields with {*} are required.', ['{*}' => '<span class="required">*</span>']); ?>
    </p>

	<?php echo $form->errorSummary($model); ?>
    <?php if ($recurrence) echo $form->errorSummary($recurrence); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
    <hr/>

    <?php
    if (isset($recurrence))
        $this->renderPartial('_form_recurrence', ['form' => $form,  'model' => $recurrence]);
    ?>


    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? _t('Create') : _t('Save'), array('class' => 'btn btn-block btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->