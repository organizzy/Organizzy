<?php
/* @var $this DepartmentController */
/* @var $model Department */
/* @var OTabView $tabView */

$currentRole = Role::model()->findFor($model->organization_id, $this->userId);

if ($currentRole->isSuperAdmin || ($currentRole->isAdmin && $currentRole->department_id = $model->id)) {
    $this->menu=[
        ['label' => _t('Add Event'), 'url' => array('/event/create',
            'type' => Event::TYPE_DEPARTMENT, 'oid' => $model->organization_id, 'did' => $model->id)],
        ['label' => _t('Add Task'), 'url' => array('/task/create',
            'type' => Task::TYPE_DEPARTMENT, 'oid' => $model->organization_id, 'did' => $model->id)],

        'Department',
        ['label'=> _t('Edit'), 'url'=>array('update', 'id'=>$model->id)],
        ['label'=> _t('Delete'), 'url'=>['delete', 'id' => $model->id],
              'options' => [
                  'class' => 'btn-post', 'data-post' => 'confirm=1', 'data-ask' => _t('Delete this department?')
              ]
        ],

        'Member',
        ['label' => _t('Invite'), 'url' => ['/role/invite', 'id' => $model->organization_id, 'department' => $model->id]],
        ['label' => _t('Manage'), 'url' => ['/role/manage', 'id' => $model->organization_id, 'department' => $model->id]],
    ];
}

$this->layoutSingle($this->createUrl('/organization/view', ['id' => $model->organization_id]) . '#tab=tab-department');
$this->pageTitle = $model->name;
?>


<?php $tabView = $this->beginWidget('OTabView', ['id' => 'organization-view']); ?>
    <?php $tabView->beginPage(_t('Info')); ?>
        <div class="content-padded">
            <p><?php echo CHtml::encode($model->description); ?></p>
        </div>
        <?php $this->renderPartial('//role/_list', ['rules' => $model->roles, 'canEdit' => $currentRole->isSuperAdmin || ($currentRole->isAdmin && $currentRole->organization_id == $model->organization_id)]) ?>
        <p class="text-center">
            <?php if ($currentRole->isAdmin)
                echo CHtml::link(_t('Invite'),
                    ['/role/invite', 'id' => $model->organization_id, 'department' => $model->id], ['class' => 'btn']), '&nbsp;',
                CHtml::link(_t('Manage'),
                    ['/role/manage', 'id' => $model->organization_id, 'department' => $model->id], ['class' => 'btn']) ?>
        </p>
    <?php $tabView->endPage(); ?>

    <?php $tabView->beginPage(_t('Event'), [], 'tab-event'); ?>
        <?php if (count($events = Event::model()->onlyDepartments($model->id)->findAll()) > 0): ?>
            <?php $this->renderPartial('//event/_list', ['models' => $events]); ?>
        <?php else: ?>
            <div class="content-padded content-empty text-right">
                <?php _p('No event added') ?>
            </div>
        <?php endif ?>
        <p class="text-center">
            <?php if ($currentRole->isAdmin)
                echo CHtml::link(_t('Add Event'),
                    ['/event/create', 'type' => Event::TYPE_DEPARTMENT, 'oid' => $model->organization_id, 'did' => $model->id], ['class' => 'btn'])
            ?>
        </p>
    <?php $tabView->endPage(); ?>


    <?php $tabView->beginPage(_t('Task'), [], 'tab-task'); ?>

        <?php if (count($tasks = Task::model()->onlyDepartment($model->id)->findAll()) > 0): ?>
            <?php $this->renderPartial('//task/_list', ['models' => $tasks]); ?>
        <?php else: ?>
            <div class="content-padded content-empty text-right">
                <?php _p('No task added') ?>
            </div>
        <?php endif ?>

        <p class="text-center">
            <?php if ($currentRole->isAdmin)
                echo CHtml::link(_t('Add Event'),
                    ['/task/create', 'type' => Task::TYPE_DEPARTMENT, 'oid' => $model->organization_id, 'did' => $model->id], ['class' => 'btn'])
            ?>
        </p>
    <?php $tabView->endPage(); ?>

<?php $this->endWidget() ?>
