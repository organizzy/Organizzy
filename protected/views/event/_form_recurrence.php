<?php
/* @var $this EventController */
/* @var $model EventRecurrence */
/* @var $form CActiveForm */

$createForm = !isset($form);
?>
<?php if ($createForm) $form=$this->beginWidget('CActiveForm', array(
        'id'=>'event-form',
        'enableAjaxValidation'=>false,
    )); ?>
<?php if ($createForm) echo $form->errorSummary($model); ?>
    <div class="row input">
        <?php echo $form->labelEx($model,'place'); ?>
        <?php echo $form->textField($model,'place'); ?>
        <?php echo $form->error($model,'place'); ?>
    </div>

    <!--div class="row input cb-toggle">
        <?php echo $form->labelEx($model, 'vote_status') ?>
        <?php echo $form->checkBox($model, 'vote_status', [
                'value' => EventRecurrence::VOTE_OPEN,
                'uncheckValue' => EventRecurrence::VOTE_CLOSED,
                'onchange' => '$("#recurrence-time").attr("class",this.checked?"vote-time":"fixed-time")',
            ]); ?>
    </div -->
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
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn btn-block btn-primary')); ?>
    </div>
<?php $this->endWidget();
endif ?>