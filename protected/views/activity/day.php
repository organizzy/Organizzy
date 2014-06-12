<?php
/**
 * @var ActivityController $this
 * @var Activity[] $models
 * @var string $date
 */

$this->layoutSingle(array('index', 'y' => substr($date, 0, 4), 'm' => substr($date, 5, 2)));
$this->pageTitle = _t('Activity on {day}', ['{day}' => O::app()->dateFormatter->formatDateTime($date, 'medium', false)]);
$this->renderPartial('_list', array('models' => $models));
