<?php
/* @var $this Controller */
/* @var $model User */
/* @var $form CActiveForm */

$this->layoutSingle(array('view'));
$this->pageTitle = _t('Edit Account')

?>
<div class="content-padded">
    <?php $form=$this->beginWidget('CActiveForm'); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row input">
        <?php echo $form->label($model,'old_password'); ?>
        <?php echo $form->passwordField($model,'old_password'); ?>
        <?php echo $form->error($model,'old_password'); ?>
    </div>
    <hr />

    <div class="row input">
        <?php echo $form->label($model,'email'); ?>
        <?php echo $form->emailField($model,'email'); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>

    <div class="row input">
        <?php echo $form->label($model,'password1', ['label' => _t('New Password')]); ?>
        <?php echo $form->passwordField($model,'password1'); ?>
        <?php echo $form->error($model,'password1'); ?>
    </div>

    <div class="row input">
        <?php echo $form->label($model,'password2'); ?>
        <?php echo $form->passwordField($model,'password2'); ?>
        <?php echo $form->error($model,'password2'); ?>
    </div>

    <button type="submit" class="btn btn-block"><?php _p('Update Account') ?></button>
</div>


<?php $this->endWidget(); ?>
