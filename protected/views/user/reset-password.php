<?php
/**
 *
 * @var UserController $this
 */

$this->layoutSingle(array('login'));
$this->pageTitle = O::t('organizzy', 'Reset Password');

?>
<div class="content-padded">
    <form method="post">
        <p>To reset your password, enter your email address. Your password will be sent to this email.</p>
        <div class="row">
            <label for="input-email"><?php echo O::t('organizzy', 'Email Address') ?></label>
            <input type="text" id="input-email" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email'] ?>">
        </div>
        <?php echo CHtml::submitButton(O::t('organizzy', 'Reset Password'), ['class' => 'btn btn-block btn-primary']) ?>
    </form>
</div>