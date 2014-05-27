<?php
/* @var $this Controller */
/* @var Department[] $departments */

?>
<?php $this->widget('OListView', [
        'models' => $departments,
        'linkAttr' => function($dep, $widget){
                /* @var Department $dep */
                /* @var OListView $widget */
                return $widget->controller->createUrl('/department/view', ['id' => $dep->id]);
            }
    ]
); ?>