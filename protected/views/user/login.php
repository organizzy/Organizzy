<?php
/* @var $this Controller */
/* @var $model LoginForm */
/* @var $form CActiveForm */

$this->layout = '//layouts/main';

?>
<div class="content" id="content-<?php echo $this->getPageId() ?>">
    <div class="content-padded">
        <img src="images/logo.png" id="logo" />

        <?php $form=$this->beginWidget('CActiveForm'); ?>
        <div class="row">
            <?php echo $form->emailField($model,'username', array('placeholder' => $model->getAttributeLabel('username'))); ?>
            <?php echo $form->error($model,'username'); ?>
        </div>
        <div class="row">
            <?php echo $form->passwordField($model,'password', array('placeholder' => $model->getAttributeLabel('password'))); ?>
            <?php echo $form->error($model,'password'); ?>
        </div>
        <div class="row">
            <button type="submit" class="btn btn-block"><?php _p('Log In') ?></button>
        </div>
        <?php /*
        <div class="row">
            <?php echo $form->checkBox($model,'rememberMe'); ?>
            <?php echo $form->label($model,'rememberMe'); ?>
            <?php echo $form->error($model,'rememberMe'); ?>
        </div>
*/ ?>
        <?php $this->endWidget(); ?>

        <div class="register-button">
            <?php echo CHtml::link(Yii::t('organizzy', 'Forgot Password'), array('forgotPassword')) ?> |
            <?php echo CHtml::link(Yii::t('organizzy', 'Register'), array('register')) ?>
        </div>

    </div>


</div><!-- #content -->