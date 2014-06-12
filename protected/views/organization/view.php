<?php

/* @var OrganizationController $this */
/* @var Organization $model */
/* @var OTabView $tabView */

$this->layout = '//layouts/single';
$this->pageTitle = _t('Organization');
$this->backUrl = $this->createUrl('index');

/** @var Role $role */
$role = Role::model()->findByPk(array('user_id' => O::app()->user->id, 'organization_id' => $model->id ));

if ($role->isSuperAdmin) {
    $this->menu = [
        ['label' => _t('Add Event'), 'url' => array('/event/create', 'oid' => $model->id, 'type' => Event::TYPE_ORGANIZATION)],
        ['label' => _t('Add Admin-Only Event'), 'url' => array('/event/create', 'oid' => $model->id, 'type' => Event::TYPE_ADMINS),
            'enable' => $role->isSuperAdmin],
        'Organization',
        ['label' => _t('Add Department'), 'url' => array('/department/create', 'oid' => $model->id)],
        ['label' => _t('Edit'), 'url' => array('update', 'id' => $model->id)],
        ['label' => _t('Delete'), 'url' => array('delete', 'id' => $model->id)],
        'Member',
        ['label' => _t('Invite'), 'url' => array('/role/invite', 'id' => $model->id)],
        ['label' => _t('Kick'), 'url' => array('/role/manage', 'id' => $model->id)],
        ['label' => _t('Leave'), 'url' => array('leave', 'id' => $model->id)],
    ];
}
elseif ($role->isAdmin) {
    $this->menu = [
        array('label' => _t('Leave'), 'url' => array('leave', 'id' => $model->id))
    ];
}
else {
    $this->menu = [
        ['label' => _t('Add Event'), 'url' => array('/event/create', 'oid' => $model->id, 'type' => Event::TYPE_ORGANIZATION)],
        'Member',
        array('label' => _t('Leave'), 'url' => array('leave', 'id' => $model->id))
    ];
}

?>
<?php $this->renderPartial('//layouts/_profile_header',[
        'name' => $model->name, 'description' => $model->description, 'photo' => $model->logo,
        'photoUploadAction' => ($role->isSuperAdmin ? ['uploadPhoto', 'id' => $model->id] : false)
    ]) ?>
<?php
if ($role->status == Role::STATUS_INVITED) {

    echo '<div class="content-padded">',
        CHtml::beginForm(array('confirmInvitation', 'id' => $model->id), 'post'),
        CHtml::hiddenField('confirm', 1),
        CHtml::submitButton('Confirm Invitation', array('class' => 'btn btn-block')),
        CHtml::endForm(),
    '</div>'
    ;
    return;
}

?>

<?php $tabView = $this->beginWidget('OTabView', ['id' => 'organization-view']); ?>
    <?php $tabView->beginPage(_t('Info')); ?>
        <div class="content-padded">
            <p><?php echo CHtml::encode($model->info); ?></p>
        </div>
        <?php $this->renderPartial('//role/_list', ['rules' => $model->adminsRole, 'canEdit' => $role->isSuperAdmin]) ?>
        <p class="text-center">
            <?php if ($role->isAdmin) echo CHtml::link(_t('Invite Admin'), ['/role/invite', 'id' => $model->id], ['class' => 'btn']) ?>
        </p>
    <?php $tabView->endPage(); ?>

    <?php $tabView->beginPage(_t('Department'), [], 'tab-department'); ?>
        <?php $this->renderPartial('//department/_list', ['departments' => $model->departments ]) ?>
        <p class="text-center">
            <?php if ($role->isAdmin) echo CHtml::link(_t('Add Department'), ['/department/create', 'oid' => $model->id], ['class' => 'btn']) ?>
        </p>
    <?php $tabView->endPage(); ?>

    <?php $tabView->beginPage(_t('Events'), [], 'tab-event'); ?>
        <?php if (count($events = Event::model()->onlyOrganization($model->id, $role->isSuperAdmin)->findAll()) > 0): ?>
        <?php $this->renderPartial('//event/_list', ['models' => $events]); ?>
        <?php else: ?>
            <div class="content-padded content-empty text-right">
                <?php _p('No Event added') ?>
            </div>
        <?php endif ?>
        <p class="text-center">
            <?php if ($role->isAdmin) echo CHtml::link(_t('Add Event'),
                ['/event/create', 'oid' => $model->id, 'type' => Event::TYPE_ORGANIZATION], ['class' => 'btn']) ?>
        </p>
    <?php $tabView->endPage(); ?>

<?php $this->endWidget() ?>


