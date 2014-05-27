<?php
/* @var $this Controller */
/* @var Event[] $models */


$this->widget('OListView', [
        'models' => $models,
        'linkAttr' => function($event) {
                /** @var Event $event */
                return ['/event/view', 'id' => $event->id];
            },
        'titleAttr' => 'title',
    ]
);
