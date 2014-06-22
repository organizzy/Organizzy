<?php
/**
 * @var Mailer $this
 * @var User $model
 */

$this->title = _t('Email Confirmation')
?>
<h3><?php echo $this->title ?></h3>
<p><?php _p('To confirm your email address use activation code below:') ?></p>
<div style="font-size: 2em; font-weight: bold; text-align: center">
    <?php echo $model->activation_code ?>
</div>