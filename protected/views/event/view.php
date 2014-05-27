<?php
// argument
/* @var $this EventController */
/* @var $model Event */
/* @var EventRecurrence $recurrence */

// internal
/* @var $form CActiveForm */
/* @var OTabView $tabView */

$canUpdate = O::app()->accessRule->canUpdate($model);
if ($canUpdate) {
    $this->menu=array(
        ['label'=>'Add Recurrence', 'url'=>['addRecurrence', 'id'=>$model->id]],
        ['label' => 'Edit Time', 'url' => ['editRecurrence', 'id' => $model->id, 'rid' => $recurrence->id],
            'enable' => $model->numRecurrence == 1],
        'Event',
        array('label'=>'Edit', 'url'=>array('update', 'id'=>$model->id)),
        array('label'=>'Delete', 'url'=>array('delete', 'id'=>$model->id),
              'options' => ['class' => 'btn-post', 'data-post' => 'confirm=1', 'data-ask' => 'Are you sure?']),
    );
}

$this->layoutSingle($this->getBackUrlByModel($model));
$this->pageTitle = $model->getTypeDescription();

?>
<div class="content-padded">
    <h2>
        <i class="fa fa-clock-o"></i> <?php echo CHtml::encode($model->title); ?>
    </h2>
</div>

<div class="content-padded">
    <?php if ($model->numRecurrence > 1) : ?>
        <?php echo CHtml::beginForm(['view', 'id' => $model->id], 'get') ?>
        <?php echo CHtml::dropDownList('rid', $recurrence->id, $model->recurrences,
            ['onchange' => '$(this.form).submit()', 'style'=>'margin-bottom:5px']) ?>
        <p style="text-align: right">
            <?php echo CHtml::link('Edit', ['editRecurrence', 'id' => $model->id, 'rid' => $recurrence->id],
                ['class' => 'btn btn-primary']) ?>
            <?php echo CHtml::link('Delete', ['deleteRecurrence', 'id' => $model->id, 'rid' => $recurrence->id],
                ['class' => 'btn btn-post', 'data-post' => '{confirm=1}', 'data-ask' => 'Delete this recurrence?']) ?>
        </p>
        <?php echo CHtml::endForm(); ?>
    <?php endif ?>

</div>

<?php $tabView = $this->beginWidget('OTabView', ['id' => 'organization-view']); ?>
    <?php $tabView->beginPage('Details'); ?>
    <div class="content-padded">
        <?php $this->renderPartial('_detail', ['model' => $model, 'recurrence' => $recurrence ]) ?>
    </div>
    <?php $tabView->endPage() ?>

    <?php if ($model->type != Event::TYPE_PERSONAL) : ?>
        <?php
        $users = $model->getUsers($recurrence->id);
        $myAttendance = isset($users[$this->userId]) ? $users[$this->userId]->attendance : null;
        ?>

        <?php $tabView->beginPage('Attendance'); ?>
            <?php if (isset($users[$this->userId]) && $recurrence->date >= date('Y-m-d')) : ?>
                <div class="content-padded">
                    <?php $this->renderPartial('_confirm',[
                            'model' => $users[$this->userId]->attendance ?: new EventAttendance(),
                            'recurrence' => $recurrence,
                        ]); ?>
                </div>
            <?php endif ?>
            <?php $this->renderPartial('_attendance', ['users' => $users ]) ?>
        <?php $tabView->endPage(); ?>

    <?php endif ?>
<?php $this->endWidget(); ?>
