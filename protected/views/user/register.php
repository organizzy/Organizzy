<?php
/* @var $this Controller */
/* @var $model User */
/* @var $form CActiveForm */

$this->layoutSingle(array('login'));
$this->pageTitle = 'Register'

?>
<div class="content-padded">
<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'user-register',
        'enableClientValidation'=>false,
        'clientOptions'=>array(
            'validateOnSubmit'=>false,
        ),
    )); ?>

<?php echo $form->errorSummary($model); ?>

<div class="row">
    <?php echo $form->label($model,'email'); ?>
    <?php echo $form->emailField($model,'email'); ?>
    <?php echo $form->error($model,'email'); ?>
</div>

<div class="row">
    <?php echo $form->label($model,'password1'); ?>
    <?php echo $form->passwordField($model,'password1'); ?>
    <?php echo $form->error($model,'password1'); ?>
</div>

<div class="row">
    <?php echo $form->label($model,'password2'); ?>
    <?php echo $form->passwordField($model,'password2'); ?>
    <?php echo $form->error($model,'password2'); ?>
</div>
<hr />

<div class="row">
    <?php echo $form->label($model,'name'); ?>
    <?php echo $form->textField($model,'name'); ?>
    <?php echo $form->error($model,'name'); ?>
</div>

    <button type="submit" class="btn btn-block"><?php echo O::t('organizzy', 'Register') ?></button>
</div>


<?php $this->endWidget(); ?>
