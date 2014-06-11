<?php
/* @var $this Controller */
/* @var $model Role */
/* @var $form CActiveForm */

?>
<?php $form=$this->beginWidget('CActiveForm'); ?>

    <?php if ($model->isNewRecord): ?>
    <div class="row input">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->emailField($model,'email',array('maxlength'=>64)); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>
    <?php endif ?>
<?php /*
    <div class="row input">
        <?php echo $form->labelEx($model,'department_id'); ?>
        <?php echo $form->dropDownList($model, 'department_id', $model->organization->departments,[
                'empty' => [0 => '< ' . $model->organization->name . ' >'],
                'onchange' => '$("#invite-admin-row")[this.value==0?"hide":"show"]()',
            ]); ?>
        <?php echo $form->error($model,'department_id'); ?>
    </div>
 */ ?>

    <div class="row input" id="position-select-row">
        <?php echo $form->labelEx($model, 'position') ?>
        <?php $this->renderPartial('_position', ['model' => $model]) ?>
        <?php echo $form->error($model,'position'); ?>
    </div>


<?php if ($model->department_id) : ?>
    <div class="row input cb-toggle">
        <?php echo $form->labelEx($model,'is_admin'); ?>
        <?php echo $form->checkBox($model,'is_admin', array('value' => 1)); ?>
        <div class="cb-toggle-handle"></div>
    </div>
<?php endif; ?>

    <div class="row buttons">
        <?php echo CHtml::submitButton(
            $model->isNewRecord ? _t('Invite to {organization}', ['{organization}' => $model->organization->name]) :
                _t('Save'),
            array('class' => 'btn btn-block btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>