<?php
/* @var $this Controller */
/* @var Task[] $models */


$this->widget('OListView', [
        'models' => $models,
        'linkAttr' => function($model) {
                /** @var Task $model */
                return ['/task/view', 'id' => $model->id];
            },

        'descriptionAttr' => function($model) {
                /** @var Task $model */
                return ($model->done ? '<span class="fa fa-check-square"></span> ' : '') . $model->description;
            },

        'titleAttr' => 'title',
    ]
);
