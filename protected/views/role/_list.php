<?php
/* @var $this Controller */
/* @var $checkBoxName string */
/* @var Role[] $rules */

if (!isset($canEdit)) $canEdit = false;

echo '<div class="card">';
$this->widget('OListView', [
        'models' => $rules,

        'titleAttr' => function($model) {
                /** @var Role $model */
                return $model->user->name;
            },

        'descriptionAttr' => function($model) {
                /** @var Role $model */
                return
                    ($model->status == Role::STATUS_INVITED ? '<i class="fa fa-plus-circle"></i> ' : '') .
                    ($model->getIsAdmin(true) ? '<i class="fa fa-briefcase"></i> ' : '') .
                    $model->position;
            },

        'photoAttr' => function($model) {
                /** @var Role $model */
                return $model->user->photo ?: O::app()->baseUrl . '/images/dummy_person.gif';
            },


        'linkAttr' => function($model) use ($canEdit) {
                /** @var Role $model */
                return $canEdit ?
                    ['/role/update', 'id' => $model->organization_id, 'uid' => $model->user->id] :
                    ['/user/view', 'id' => $model->user->id, 'return' => O::app()->request->url];
            },

    ]
);
echo '</div>';
return;
?>