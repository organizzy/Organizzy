<?php
/* @var Controller $this */
/* @var EventAttendance $model */
/* @var EventRecurrence $recurrence */

/** @var CActiveForm $form */
$form = $this->beginWidget('CActiveForm', [
        'action' => ['confirm', 'id' => $recurrence->event_id, 'rid' => $recurrence->id]
    ])
?>
    <div class="row cb-toggle">
        <?php echo $form->labelEx($model, 'status', ['label' => _t('Attend?')] ) ?>
        <?php /* echo $form->radioButtonList($model, 'status', [
                EventAttendance::STATUS_ATTEND => 'Attend',
                EventAttendance::STATUS_NOT_ATTEND => 'Not Attend',
            ]) */?>
        <?php echo $form->checkBox($model, 'status', ['value' => EventAttendance::STATUS_ATTEND, 'uncheckValue' => EventAttendance::STATUS_NOT_ATTEND ]) ?>
    </div>
    <div class="row">
        <?php //echo $form->labelEx($model, 'comment') ?>
        <?php echo $form->textArea($model, 'comment'); ?>
    </div>
    <div class="row row-button" style="text-align: right; width: 200px">
        <?php echo CHtml::submitButton(_t('Submit'), ['class' => 'btn btn-primary']) ?>
    </div>
<?php $this->endWidget(); ?>