<?php

/**
 * @var ActivityController $this
 * @var Activity[] $models
 */

$this->menu=array(
    array('label'=>'Calendar', 'url'=>array('index', 'mode' => 'calendar')),
);

$this->renderPartial('_list', array('models' => $models));
