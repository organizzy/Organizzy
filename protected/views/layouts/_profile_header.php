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
                  style="background-image: url(<?php echo $photo ? $photo->getUrl('m') : O::app()->dummyPhoto ?>)"></span>
        </div>
        <h2 class="name"><?php echo CHtml::encode($name) ?></h2>
        <p class="description"><?php echo CHtml::encode($description) ?></p>
    </div>
    <div class="clearfix"></div>
</div>

<div id="photo-full" data-photo="<?php echo $photo ? $photo->getUrl() : O::app()->dummyPhoto ?>" style="display: none">
    <div class="control">
        <a href="#" class="btn" id="photo-full-back" onclick="$('#photo-full').fadeOut()"><?php _p('Back') ?></a>
        <?php if (isset($photoUploadAction) && $photoUploadAction) : $url = O::app()->getBaseUrl(true) . CHtml::normalizeUrl($photoUploadAction); ?>
            <a class="btn photo-upload" data-source="gallery" data-target="<?php echo $url ?>">
                <?php _p('Edit') ?>
            </a>
            <a class="btn photo-upload" data-source="camera" data-target="<?php echo $url ?>">
                <?php _p('Take photo') ?>
            </a>
        <?php endif ?>
    </div>
</div>