<?php

/**
 * @var Controller $this
 * @var TaskProgress $models
 */

$this->widget('OListView', [
        'models' => $models,
        'titleAttr' => function($model) {
                /** @var TaskProgress $model */
                return sprintf('[%s%%] %s', $model->progress, $model->comment);
            },

        'descriptionAttr' => function($model) {
                /** @var TaskProgress $model */

                return sprintf('by %s at %s',
                    $model->reporter->name,
                    O::app()->dateFormatter->formatDateTime($model->report_time, 'short', 'short')
                    );
            }
    ]
);