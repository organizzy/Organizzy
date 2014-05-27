<?php
/**
 * @var ActivityController $this
 * @var Activity[] $models
 * @var string $date
 */

$this->layoutSingle(array('index', 'y' => substr($date, 0, 4), 'm' => substr($date, 5, 2)));
$this->pageTitle = sprintf('Activity on %s', O::app()->dateFormatter->formatDateTime($date, 'medium', false));
$this->renderPartial('_list', array('models' => $models));
