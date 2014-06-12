<?php
// argument
/* @var $this EventController */
/* @var $model Event */
/* @var EventRecurrence $recurrence */
/* @var ODetailView $detailView */

?>

<?php $detailView = $this->beginWidget('ODetailView'); ?>
<?php $detailView->show(_t('Description'), $model->description, 'fa-info-circle') ?>
<?php $detailView->show(_t('Date'), O::app()->dateFormatter->formatDateTime($recurrence->date, 'medium', false), 'fa-calendar') ?>
<?php $detailView->show(_t('Time'), $recurrence->begin_time . ($recurrence->end_time ? (' - ' . $recurrence->end_time) : ''), 'fa-clock-o') ?>
<?php $detailView->show(_t('Place'), $recurrence->place, 'fa-map-marker') ?>
<?php $this->endWidget(); ?>