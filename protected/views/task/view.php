<?php
/**
 *
 * @var TaskController $this
 * @var Task $model
 *
 * @var CActiveForm $form
 * @var ODetailView $detailView
 */

$this->layoutSingle($this->getBackUrlByModel($model));
$this->pageTitle = 'Task';

$canEdit = $this->rule->checkUpdateAccess($model, false);
if ($canEdit) {
    $this->menu = [
        ['label' => 'Edit', 'url' => ['update', 'id' => $model->id]],
        ['label' => 'Delete', 'url' => ['delete', 'id' => $model->id],
         'options' => ['class' => 'btn-post', 'data-post' => 'confirm=1', 'data-ask' => 'Are you sure?']],
    ];
}

?>
<div class="content-padded">
    <h2>
        <i class="fa fa-tasks"></i> <?php echo CHtml::encode($model->title); ?>
    </h2>
    <?php
    if ($canEdit) {
        echo Tag::div()->addClass('text-right')
            ->append(Tag::link(['update', 'id' => $model->id], 'Edit')->addClass('btn btn-primary'));
    }

    $detailView = $this->beginWidget('ODetailView');
    $detailView->show('Description', CHtml::encode($model->description), 'fa-info-circle');
    $detailView->show('Due time', O::app()->dateFormatter->formatDateTime($model->deadline), 'fa-clock-o');

    if ($model->type == Task::TYPE_DEPARTMENT) {
        $detailView->show('Created by', $model->owner->name, 'fa-male');
        $detailView->show('Department', $model->department->name . ', ' . $model->department->organization->name , 'fa-briefcase');
        $detailView->show('Assign to', implode(', ', $model->assignedUsers), 'fa-group');

    }
    $this->endWidget();

    ?>

    <?php if ($model->type == Task::TYPE_PERSONAL) : ?>
    <form action="<?php echo $this->createUrl('changeStatus', ['id' => $model->id]) ?>" method="post">
        <div class="row cb-toggle input">
            <input type="hidden" name="done" value="0">
            <label for="cb-change-status">Done</label>
            <input id="cb-change-status" name="done" value="1"
                   <?php if ($model->done) echo 'checked' ?> onchange="$(this.form).submit()" type="checkbox" />
            <div class="cb-toggle-handle"></div>
        </div>
    </form>
    <?php else : ?>
        <?php $this->renderPartial('_progress', ['models' => $model->progresses]) ?>
        <?php if (! $model->done) : ?>
            <?php
            $form = $this->beginWidget('CActiveForm', ['action' => ['addProgress', 'id' => $model->id]]);
            $progress = new TaskProgress();
            $lastProgress = O::app()->db->createCommand()->select('MAX(progress)')->from('task_progress')
                ->where('task_id = :tid')->queryScalar([':tid' => $model->id]) ?: 0;
            ?>
            <h4>Add Progress</h4>
            <div class="row input">
                <?php echo $form->labelEx($progress, 'progress') ?>
                <?php echo $form->numberField($progress, 'progress', ['value' => $lastProgress + 1, 'min' => $lastProgress + 1, 'max' => 100]) ?>
                <?php echo $form->error($progress, 'progress') ?>
            </div>
            <div class="row input">
                <?php echo $form->labelEx($progress, 'comment') ?>
                <?php echo $form->textArea($progress, 'comment') ?>
                <?php echo $form->error($progress, 'comment') ?>
            </div>
            <div class="row">
                <?php echo CHtml::submitButton('Submit', ['class' => 'btn btn-block btn-primary']) ?>
            </div>
            <?php $this->endWidget() ?>
        <?php endif ?>
    <?php endif ?>
</div>
