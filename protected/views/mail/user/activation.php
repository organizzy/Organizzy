<?php
/**
 * @var Mailer $this
 * @var User $model
 */

$this->title = 'Email Confirmation'
?>
<h3>Email Confirmation</h3>
<p>To confirm your email address use activation code below:</p>
<div style="font-size: 2em; font-weight: bold; text-align: center">
    <?php echo $model->activation_code ?>
</div>