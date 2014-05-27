<?php

/**
 * @var ActivityController $this
 * @var Activity[] $models
 */

$userPhoto = Photo::model()->find([
        'join' => 'JOIN "user" ON "user".photo_id = t.id AND "user".id = :uid',
        'params' => [':uid' => $this->userId],
    ]
) ?: O::app()->getDummyPhoto();

$this->widget('OListView', [
        'models' => $models,

        'data' => [
            'user-photo' => $userPhoto,
        ],

        'titleAttr' => function($model) {
                /** @var Activity $model */
                return $model->getTitle();
            },

        'descriptionAttr' => function($model) {
                /** @var Activity $model */
                $icons = [];
                if ($model->isEvent()) {
                    $icons[] = 'clock-o';
                    if ($model->subType == Event::TYPE_ORGANIZATION) {
                        $icons[] = 'group';
                    }
                    elseif ($model->subType == Event::TYPE_ADMINS) {
                        $icons[] = 'briefcase';
                    }
                    elseif ($model->subType == Event::TYPE_DEPARTMENT) {
                        $icons[] = 'sitemap';
                    }
                } elseif ($model->isTask()) {
                    $icons[] = 'tasks';
                    if ($model->subType != Task::TYPE_PERSONAL) {
                        $icons[] = 'group';
                    }
                }

                $iconStr = '';
                foreach($icons as $icon) {
                    $iconStr .= '<span class="fa fa-' . $icon . '"></span> ';
                }
                return $iconStr . O::app()->dateFormatter->formatDateTime($model->datetime);
            },

        'photoAttr' => function($model, $widget) {
                /** @var Activity $model */
                /** @var OListView $widget */

                if (($model->isEvent() && $model->subType == Event::TYPE_PERSONAL)
                 || ($model->isTask() && $model->subType == Task::TYPE_PERSONAL))
                    return $widget->data['user-photo'];
                else
                    return $model->organization->logo;
            },

        'linkAttr' => function($model) {
                /** @var Activity $model */
                if ($model->isEvent()) {
                    $url =  ['/event/view', 'id' => $model->event->id, 'rid' => $model->recurrence_id];
                } elseif ($model->isTask()) {
                    $url = ['/task/view', 'id' => $model->task_id];
                } else {
                    return null;
                }

                //$url[0] .= '#return=' . O::app()->request->url;
                $url = CHtml::normalizeUrl($url) . '#return=' . O::app()->request->url;
                return $url;
            }
    ]
);

?>
