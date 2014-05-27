<?php
/* @var Controller */
/* @var string $name */
/* @var string $description */
/* @var Photo $photo */
/* @var string $photoUploadAction */

/* @var CActiveForm $form */

?>
<div class="profile-header">
    <div class="content-padded">
        <div class="photo-container">
            <span class="photo" id="profile-photo"
                  style="background-image: url(<?php echo $photo ? $photo->getUrl('m') : O::app()->baseUrl . '/images/dummy_person.gif' ?>)"></span>
        </div>
        <h2 class="name"><?php echo CHtml::encode($name) ?></h2>
        <p class="description"><?php echo CHtml::encode($description) ?></p>
    </div>
    <div class="clearfix"></div>

<?php if (isset($photoUploadAction) && $photoUploadAction) : $model = new Photo(); ?>
    <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'photo-upload-form',
            'action' => $photoUploadAction,
            'htmlOptions' => ['enctype' => 'multipart/form-data', 'class' => 'photo-upload'],
        ));

    echo $form->fileField($model, 'file', ['accept' => 'image/jpeg,image/png,image/gif']);
    //echo CHtml::fileField('file', '', ['id' => 'Photo_file']);
    echo CHtml::submitButton('alala');
    ?>

    <?php $this->endWidget() ?>
<?php endif; ?>
</div>
