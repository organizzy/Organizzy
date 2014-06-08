<?php
/**
 * @var Mailer $this
 * @var User $model
 */

$this->title = 'User Registration'
?>
<h3>Thank you for using Organizzy, <?php echo $model->name ?></h3>
<p>You can login to Organizzy by running Organizzy login with email address <?php echo $model->email ?></p>
<p>You can not access all features in Organizzy until you activate your email address.
    To activate it use activation code below:</p>
<div style="font-size: 2em; font-weight: bold; text-align: center">
    <?php echo $model->activation_code ?>
</div>