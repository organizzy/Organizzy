<?php
/* @var $this TaskController */
/* @var Task[] $models */
/* @var bool $all */
if ($all) {
    $this->layoutSingle(['index']);
    $this->pageTitle = _t('Show All');
}
$this->menu = [
    _t('Task'),
    ['label'=>_t('Create'), 'url'=>array('create')],
    ['label'=>_t('Show All'), 'url'=>array('index', 'all' => true), 'enable' => ! $all],
];

if (count($models) > 0) :
    $this->renderPartial('_list', ['models' => $models]);
else:
    ?>
    <div class="content-padded content-empty text-right">
        <?php _p('No Task added') ?>
    </div>
<?php endif ?>
<p class="text-center"><?php echo CHtml::link(_t('Create new'), ['create'], ['class' => 'btn']) ?></p>
