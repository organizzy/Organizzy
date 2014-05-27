<?php

/* @var Controller $this */
/* @var User[] $users */

$this->widget('OListView', [
        'models' => $users,
        'descriptionAttr' => function($model) {
                /** @var User $model */
                $status = $model->attendance ? $model->attendance->status : EventAttendance::STATUS_UNKNOWN;
                $comment = $model->attendance ? ': ' . $model->attendance->comment : '';
                return EventAttendance::getStatusStrings($status) . $comment;
            },

        'dividerCb' => function($model) {
                static $status = null;
                /** @var User $model */
                $newStatus = $model->attendance ? $model->attendance->status : 'Not Responded';
                if ($status != $newStatus) {
                    //return $status = $newStatus;
                }
                return null;
            }
    ]
);