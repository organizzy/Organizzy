<?php

/**
 * @var OListView $this
 * @var ActiveRecord $model
 */

?>
<?php if ($this->createTag) : ?> <ul class="table-view <?php echo $this->class ?>"> <?php endif ?>
    <?php foreach($this->models as $model) : ?>
    <?php
    $url = $this->fetchAttr($model, $this->linkAttr) ?: '';
    if (is_array($url)){
        $route = array_shift($url);
        $url = $this->controller->createUrl($route, $url);
    }
    ?>
    <?php if ($dividerText = $this->getDividerText($model)) : ?>
        <li class="table-view table-view-divider"><?php echo $dividerText ?></li>
    <?php endif ?>
        <li class="table-view-cell media" id="<?php echo get_class($model), '_', $model->getPrimaryKey(true)  ?>">
            <a class="navigate-right" href="<?php echo $url ?>">
                <?php if ($photo = $this->fetchAttr($model, $this->photoAttr)) :  ?>
                    <?php if (is_a($photo, 'Photo')) $photo = $photo->getUrl('s'); ?>
                    <img class="media-object pull-left" src="<?php echo $photo ?>" />
                <?php endif ?>
                <div class="media-body">
                    <?php // if ($this->checkBox) echo $this->fetchAttr($model, $this->titleAttr) ?>
                    <?php echo $this->fetchAttr($model, $this->titleAttr) ?>
                    <p><?php echo $this->fetchAttr($model, $this->descriptionAttr) ?></p>
                </div>
            </a>
            <?php
            if ($checkBox = $this->fetchAttr($model, $this->checkBox)) {
                if (!isset($checkBox['class'])) $checkBox['class'] = 'checkbox';
                else $checkBox['class'] .= ' checkbox';
                echo CHtml::checkBox($checkBox['name'], $checkBox['checked'], $checkBox);
            }

            ?>
        </li>
    <?php endforeach ?>
<?php if ($this->createTag) : ?></ul><?php endif ?>