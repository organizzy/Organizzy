<?php
/**
 *
 * @var SiteController $this
 * @var \SettingForm $model
 *
 * @var CActiveForm $form
 */
$this->layoutSingle(['/']);
$this->pageTitle = _t('Setting');

?>

<div class="form content-padded">

    <?php $form=$this->beginWidget('CActiveForm'); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,SettingForm::PROP_LANGUAGE); ?>
        <?php echo $form->dropDownList($model,SettingForm::PROP_LANGUAGE,
        [
            'en_US' => 'English',
            'id_ID' => 'Bahasa',
        ]); ?>
        <?php echo $form->error($model,SettingForm::PROP_LANGUAGE); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton(_t('Save'), ['class' => 'btn btn-block btn-primary']); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->