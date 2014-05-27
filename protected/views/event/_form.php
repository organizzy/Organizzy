<?php
/* @var EventController $this  */
/* @var $model Event */
/* @var $recurrence EventRecurrence */
/* @var $form CActiveForm */
//
//if (! $model->begin_time) {
//    $model->begin_time = date('Y-m-d H:i');
//} else {
//    $model->begin_time = substr($model->begin_time, 0, 16);
//}
//if (! $model->end_time) {
//    $model->end_time = date('Y-m-d H:i');
//}else {
//    $model->end_time = substr($model->end_time, 0, 16);
//}
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'event-form',
	'enableAjaxValidation'=>false,
)); ?>
	<p class="note">Fields with <span class="required">*</span> are required.</p>

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
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn btn-block btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->