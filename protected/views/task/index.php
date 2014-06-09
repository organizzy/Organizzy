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

if (count($models) > 0) :
    $this->renderPartial('_list', ['models' => $models]);
else:
    ?>
    <div class="content-padded content-empty text-right">
        No Task added<br />
    </div>
<?php endif ?>
<p class="text-center"><?php echo CHtml::link(O::t('organizzy', 'Create new'), ['create'], ['class' => 'btn']) ?></p>
