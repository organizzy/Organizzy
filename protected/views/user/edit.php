<?php
/* @var $this Controller */
/* @var $model User */
/* @var $form CActiveForm */

$this->pageTitle = $model->name;
$this->layoutSingle(['view']);
?>
<div class="content-padded">
    <?php $form=$this->beginWidget('CActiveForm');  ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->label($model,'name'); ?>
        <?php echo $form->textField($model,'name'); ?>
        <?php echo $form->error($model,'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'birth_date'); ?>
        <?php echo $form->dateField($model,'birth_date', array('type' => 'date')); ?>
        <?php echo $form->error($model,'birth_date'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'city'); ?>
        <?php echo $form->textField($model,'city'); ?>
        <?php echo $form->error($model,'city'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'phone'); ?>
        <?php echo $form->textField($model,'phone'); ?>
        <?php echo $form->error($model,'phone'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'aboutMe'); ?>
        <?php echo $form->textArea($model,'aboutMe'); ?>
        <?php echo $form->error($model,'aboutMe'); ?>
    </div>

    <button type="submit" class="btn btn-block"><?php _p('Update') ?></button>
</div>


<?php $this->endWidget(); ?>
