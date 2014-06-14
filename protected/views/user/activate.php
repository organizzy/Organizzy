<?php
/**
 *
 * @var UserController $this
 * @var User $model
 */

$this->pageTitle = _t('Activate User');
$this->layoutSingle(['view']);
?>
<div class="content-padded">
    <?php echo CHtml::beginForm(['activate'], 'post') ?>
    <p><?php _p('Enter your activation code that we have sent to {email}', ['{email}' => $model->email])  ?></p>
    <div class="row">
        <input type="text" name="activation_code" placeholder="<?php _p('Activation Code') ?>">
    </div>
    <?php echo CHtml::submitButton(_t('Activate'), ['class' => 'btn btn-block btn-primary']) ?>
    <hr />
    <?php _p('Not received activation code? {resend} or {change-email}',
        [
            '{resend}' => '<div>' . CHtml::link(_t('Resend Activation Code'), ['activate', 'resend' => 1], ['class' => 'btn']) . '</div>',
            '{change-email}' => '<div>' . CHtml::link(_t('Change Email Address'), ['account'], ['class' => 'btn']) . '</div>',
        ]
    ); ?>
    <?php echo CHtml::endForm() ?>
</div>