<?php
/**
 * @var Mailer $this
 * @var User $model
 */

$this->title = _t('User Registration')
?>
<h3><?php _p('Thank you for using Organizzy, {name}.', ['{name}' => $model->name]) ?></h3>
<p><?php _p('You can login to Organizzy using email address {email}', ['{email}' => $model->email]) ?></p>
<p><?php _p('To activate your account use activation code below:') ?></p>
<div style="font-size: 2em; font-weight: bold; text-align: center">
    <?php echo $model->activation_code ?>
</div>