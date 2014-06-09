<?php

/**
 * @var ActivityController $this
 * @var Activity[] $models
 */

$this->menu=array(
    array('label'=>'Calendar', 'url'=>array('index', 'mode' => 'calendar')),
);

if (count($models) > 0) :
    $this->renderPartial('_list', array('models' => $models));
else :
?>
    <div class="content-padded content-empty">
        There are no activity right now
    </div>
<?php endif;

