<?php
// argument
/* @var $this EventController */
/* @var $model Event */
/* @var EventRecurrence $recurrence */
/* @var ODetailView $detailView */

?>

<?php $detailView = $this->beginWidget('ODetailView'); ?>
<?php $detailView->show('Description', $model->description, 'fa-info-circle') ?>
<?php $detailView->show('Date', O::app()->dateFormatter->formatDateTime($recurrence->date, 'medium', false), 'fa-calendar') ?>
<?php $detailView->show('Time', $recurrence->begin_time . ($recurrence->end_time ? (' - ' . $recurrence->end_time) : ''), 'fa-clock-o') ?>
<?php $detailView->show('Place', $recurrence->place, 'fa-map-marker') ?>
<?php $this->endWidget(); ?>