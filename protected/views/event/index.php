<?php
/* @var $this EventController */
/* @var Event[] $models */
/* @var bool $all */
if ($all) {
    $this->layoutSingle(['index']);
    $this->pageTitle = 'All Events';
}
$this->menu = [
    'Event',
    ['label'=>'Create', 'url'=>array('create')],
    ['label'=>'Show All', 'url'=>array('index', 'all' => true), 'enable' => ! $all],
];

$this->renderPartial('_list', ['models' => $models]);
