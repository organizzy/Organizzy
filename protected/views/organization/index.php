<?php

/* @var $this Controller */
/* @var $organizations Organization[] */
/* @var boolean $all */

$jointStatuses = array(
    'Invited', 'Joint', 'Old'
);

if ($all) {
    $this->layoutSingle(['index']);
    $this->pageTitle = 'All Organization';
} else {
    $this->menu = array(
        'Organization',
        ['label' => O::t('organizzy', 'Show All'), 'url' => ['index', 'all' => true]],
        ['label' => O::t('organizzy', 'Create New'), 'url' => array('create')],
    );
}


$curStatus = -1;
$controller = $this;

$this->widget('OListView', [
        'models' => $organizations,
        'linkAttr' => function($org) {
                /** @var Organization $org */
                return O::app()->createUrl('/organization/view', array('id' => $org->id));
            },
        'photoAttr' => function($org) {
                /** @var Organization $org */
                return $org->logo ?: O::app()->dummyPhoto;
        },

        'dividerCb' => function($org) {
                /** @var Organization $org */
                static $currentStatus = Role::STATUS_JOINT;

                if ($org->role->status != $currentStatus) {
                    $currentStatus = $org->role->status;
                    return $currentStatus;
                }
                return null;
            }
    ]
);
return;

?>
