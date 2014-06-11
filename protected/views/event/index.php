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

if (count($models) > 0) :
    $this->renderPartial('_list', ['models' => $models]);
else:
?>
    <div class="content-padded content-empty">
        <?php _p('No event added'); ?>
    </div>
<?php endif ?>
<p class="text-center"><?php echo CHtml::link(_t('Create new'), ['create'], ['class' => 'btn']) ?></p>
