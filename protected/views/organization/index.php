<?php

/* @var $this Controller */
/* @var $organizations Organization[] */
/* @var boolean $all */
?>
<?php if (! User::model()->findByPk($this->userId)->getActivated()) : ?>
    <div class="content-padded">
        <strong><?php _p('Your account has not been activated'); ?></strong>
        <?php echo CHtml::link(_t('Activate now'), ['/user/activate'], ['class' => 'btn btn-block btn-primary']) ?>
    </div>
    <?php return; ?>
<?php endif ?>
<?php

if ($all) {
    $this->layoutSingle(['index']);
    $this->pageTitle = 'All Organization';
} else {
    $this->menu = array(
        'Organization',
        ['label' => _t('Show All'), 'url' => ['index', 'all' => true]],
        ['label' => _t('Create New'), 'url' => array('create')],
    );
}

if (count($organizations) > 0) :
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

                        switch($currentStatus) {
                            case Role::STATUS_INVITED:
                                return _t('Invited');
                            case Role::STATUS_JOINT:
                                return _t('Joint');
                            case Role::STATUS_ARCHIVED:
                                return _t('Old');
                            default:
                                return $currentStatus;
                        }

                    }
                    return null;
                }
        ]
    );

else : ?>
    <div class="content-padded content-empty">
        No Organization added<br />
    </div>
<?php endif ?>
<p class="text-center"><?php echo CHtml::link(_t('Create new'), ['create'], ['class' => 'btn']) ?></p>