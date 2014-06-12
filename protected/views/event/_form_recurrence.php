<?php
/* @var $this EventController */
/* @var $model EventRecurrence */
/* @var $form CActiveForm */

$createForm = !isset($form);
?>
<?php if ($createForm) $form=$this->beginWidget('CActiveForm'); ?>
<?php if ($createForm) echo $form->errorSummary($model); ?>
    <div class="row input">
        <?php echo $form->labelEx($model,'place'); ?>
        <?php echo $form->textField($model,'place'); ?>
        <?php echo $form->error($model,'place'); ?>
    </div>

    <div id="recurrence-time" class="fixed-time">
        <div id="fixed-time">
            <div class="row">
                <?php echo $form->labelEx($model,'date'); ?>
                <?php echo $form->dateField($model,'date'); ?>
                <?php echo $form->error($model,'date'); ?>
            </div>
            <div class="row">
                <?php echo $form->labelEx($model,'begin_time'); ?>
                <?php echo $form->timeField($model,'begin_time'); ?>
                <?php echo $form->error($model,'begin_time'); ?>
            </div>
            <div class="row">
                <?php echo $form->labelEx($model,'end_time'); ?>
                <?php echo $form->timeField($model,'end_time'); ?>
                <?php echo $form->error($model,'end_time'); ?>
            </div>
        </div>

        <div id="vote-time">

        </div>
    </div>
<?php if ($createForm): ?>
    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? _t('Create') : _t('Save'), array('class' => 'btn btn-block btn-primary')); ?>
    </div>
<?php $this->endWidget();
endif ?>