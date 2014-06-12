<?php
/**
 * @var TaskController $this
 * @var Task $model
 * @var CActiveForm $form
 *
 */
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm'); ?>
    <p class="note"><?php _p('Fields with {*} are required.', ['{*}' => '<span class="required">*</span>']); ?></p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row input">
        <?php echo $form->labelEx($model,'title'); ?>
        <?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>64)); ?>
        <?php echo $form->error($model,'title'); ?>
    </div>

    <div class="row input">
        <?php echo $form->labelEx($model,'description'); ?>
        <?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
        <?php echo $form->error($model,'description'); ?>
    </div>

    <div class="row input">
        <?php echo $form->labelEx($model,'date'); ?>
        <?php echo $form->dateField($model,'date'); ?>
        <?php echo $form->error($model,'date'); ?>
    </div>

    <div class="row input">
        <?php echo $form->labelEx($model,'time'); ?>
        <?php echo $form->timeField($model,'time',array('rows'=>6, 'cols'=>50)); ?>
        <?php echo $form->error($model,'time'); ?>
    </div>

<?php if ($model->department_id) : ?>
    <div class="row">
        <h4><?php _t('Assign to:') ?></h4>
        <ul class="table-view table-view-check">

            <?php foreach(User::model()->findAll([
                    'select' => 'id, name',
                    'join' => 'JOIN role ON user_id = id AND department_id = :did',
                    'order' => 'name',
                    'params' => [':did' => $model->department_id],
                    'index' => 'id',
                ]) as $user) : $name = 'assign_to[' . $user->id . ']' ?>
            <li class="table-view-cell">
                <?php echo $form->labelEx($model, $name, ['label' => $user->name]) ?>
                <?php echo $form->checkBox($model, $name, ['value' => 1, 'class' => 'checkbox']); ?>
            </li>
            <?php endforeach; ?>
            <?php echo $form->error($model,'assign_to'); ?>
        </ul>
    </div>
<?php endif ?>
<div class="row buttons">
    <?php echo CHtml::submitButton($model->isNewRecord ? _t('Create') : _t('Save'), array('class' => 'btn btn-block btn-primary')); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->