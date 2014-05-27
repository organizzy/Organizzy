<?php
/* @var $this EventController */
/* @var $eventInvitations EventInvite[] */


$this->menu=array(
    array('label'=>'Create', 'url'=>array('create')),
);

?>

<?php

//$this->widget('zii.widgets.CListView', array(
//	'dataProvider'=>$dataProvider,
//	'itemView'=>'_view',
//));
?>
<ul class="table-view">
    <?php foreach ($eventInvitations as $invitation): $event = $invitation->event ?>
        <li class="table-view-cell media">
            <a class="navigate-right" href="<?php echo $this->createUrl('view', array('id' => $event->id)) ?>">
                <img class="media-object pull-left" src="http://placehold.it/42x42">
                <div class="media-body">
                    <?php echo CHtml::encode($event->title) ?>
                    <p><?php echo CHtml::encode($event->description) ?></p>
                </div>
            </a>
        </li>
    <?php endforeach ?>
</ul>