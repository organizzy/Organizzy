<?php

/* @var OrganizationController $this */
/* @var Organization $model */
/* @var OTabView $tabView */

$this->layout = '//layouts/single';
$this->pageTitle = 'Organization';
$this->backUrl = $this->createUrl('index');

/** @var Role $role */
$role = Role::model()->findByPk(array('user_id' => O::app()->user->id, 'organization_id' => $model->id ));

if ($role->isSuperAdmin) {
    $this->menu = [
        ['label' => 'Add Event', 'url' => array('/event/create', 'oid' => $model->id, 'type' => Event::TYPE_ORGANIZATION)],
        ['label' => 'Add Admin-Only Event', 'url' => array('/event/create', 'oid' => $model->id, 'type' => Event::TYPE_ADMINS),
            'enable' => $role->isSuperAdmin],
        'Organization',
        ['label' => O::t('organizzy', 'Add Department'), 'url' => array('/department/create', 'oid' => $model->id)],
        ['label' => O::t('organizzy', 'Edit'), 'url' => array('update', 'id' => $model->id)],
        ['label' => O::t('organizzy', 'Delete'), 'url' => array('delete', 'id' => $model->id)],
        'Member',
        ['label' => O::t('organizzy', 'Invite'), 'url' => array('/role/invite', 'id' => $model->id)],
        ['label' => O::t('organizzy', 'Kick'), 'url' => array('/role/manage', 'id' => $model->id)],
        ['label' => O::t('organizzy', 'Leave'), 'url' => array('leave', 'id' => $model->id)],
    ];
}
elseif ($role->isAdmin) {
    $this->menu = [
        array('label' => O::t('organizzy', 'Leave'), 'url' => array('leave', 'id' => $model->id))
    ];
}
else {
    $this->menu = [
        ['label' => 'Add Event', 'url' => array('/event/create', 'oid' => $model->id, 'type' => Event::TYPE_ORGANIZATION)],
        'Member',
        array('label' => O::t('organizzy', 'Leave'), 'url' => array('leave', 'id' => $model->id))
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
    <?php $tabView->beginPage('Info'); ?>
        <div class="content-padded">
            <p><?php echo CHtml::encode($model->info); ?></p>
        </div>
        <?php $this->renderPartial('//role/_list', ['rules' => $model->adminsRole, 'canEdit' => $role->isSuperAdmin]) ?>
    <?php $tabView->endPage(); ?>

    <?php $tabView->beginPage('Department', [], 'tab-department'); ?>
        <?php $this->renderPartial('//department/_list', ['departments' => $model->departments ]) ?>
    <?php $tabView->endPage(); ?>

    <?php $tabView->beginPage('Events', [], 'tab-event'); ?>
        <?php
        $this->renderPartial('//event/_list', ['models' => Event::model()->onlyOrganization($model->id, $role->isSuperAdmin)->findAll()]);
        ?>
    <?php $tabView->endPage(); ?>

<?php $this->endWidget() ?>


