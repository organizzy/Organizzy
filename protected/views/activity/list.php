<?php

/**
 * @var ActivityController $this
 * @var Activity[] $models
 */

$this->menu=[
    ['label'=> _t('Calendar'), 'url'=>['index', 'mode' => 'calendar']],
];

if (count($models) > 0) :
    $this->renderPartial('_list', array('models' => $models));
else :
?>
    <div class="content-padded content-empty">
        <?php _p('No Activities') ?>
    </div>
<?php endif;

