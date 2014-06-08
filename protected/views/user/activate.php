<?php
/**
 *
 * @var UserController $this
 * @var User $model
 */

$this->pageTitle = 'Activate User';
$this->layoutSingle(['view']);
?>
<div class="content-padded">
    <?php echo CHtml::beginForm(['activate'], 'post') ?>
    <p>Enter your activation code that we have sent to <?php echo $model->email ?></p>
    <div class="row">
        <input type="text" name="activation_code" placeholder="Activation Code">
    </div>
    <?php echo CHtml::submitButton(O::t('organizzy', 'Activate'), ['class' => 'btn btn-block btn-primary']) ?>
    <hr />
    Not received activation code?
    <?php echo CHtml::link(O::t('organizzy', 'Resend Activation Code'), ['activate', 'resend' => 1], ['class' => 'btn']) ?><br/>
    or<br/>
    <?php echo CHtml::link(O::t('organizzy', 'Change Email Address'), ['account'], ['class' => 'btn']) ?>

    <?php echo CHtml::endForm() ?>
</div>