<?php
/* @var $this TaskController */
/* @var Task[] $models */
/* @var bool $all */
if ($all) {
    $this->layoutSingle(['index']);
    $this->pageTitle = 'All Task';
}
$this->menu = [
    'Task',
    ['label'=>'Create', 'url'=>array('create')],
    ['label'=>'Show All', 'url'=>array('index', 'all' => true), 'enable' => ! $all],
];

$this->renderPartial('_list', ['models' => $models]);
