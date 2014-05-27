<?php

/* @var $menu array */
//var_dump($menu);
?>
<div id="more-menu">
    <div class="popover">
        <ul class="table-view">
            <?php

            foreach($menu as $item) {
                if (is_string($item)) {
                    echo '<li class="table-view-divider">', CHtml::encode($item), '</li>';
                }
                elseif (!isset($item['enable']) || $item['enable']) {
                    $htmlOptions = isset($item['options']) ? $item['options'] : [];

                    //var_dump($item);
                    echo '<li class="table-view-cell">', CHtml::link($item['label'], $item['url'], $htmlOptions), '</li>';
                }
            }
            ?>
        </ul>
    </div>
</div>