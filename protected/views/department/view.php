<?php
/* @var $this DepartmentController */
/* @var $model Department */
/* @var OTabView $tabView */

$currentRole = Role::model()->findFor($model->organization_id, $this->userId);
if ($currentRole->isSuperAdmin || ($currentRole->isAdmin && $currentRole->department_id = $model->id)) {
    $this->menu=array(
        ['label' => 'Add Event', 'url' => array('/event/create',
            'type' => Event::TYPE_DEPARTMENT, 'oid' => $model->organization_id, 'did' => $model->id)],
        ['label' => 'Add Task', 'url' => array('/task/create',
            'type' => Task::TYPE_DEPARTMENT, 'oid' => $model->organization_id, 'did' => $model->id)],

        'Department',
        array('label'=>'Edit', 'url'=>array('update', 'id'=>$model->id)),
        array('label'=>'Delete Department', 'url'=>['delete', 'id' => $model->id],
              'options' => ['class' => 'btn-post', 'data-post' => 'confirm=1', 'data-ask' => 'Are you sure?']),

        'Member',
        ['label' => 'invite', 'url' => ['/role/invite', 'id' => $model->organization_id, 'department' => $model->id]],
        ['label' => 'Manage', 'url' => ['/role/manage', 'id' => $model->organization_id, 'department' => $model->id]],
    );
}

$this->layoutSingle($this->createUrl('/organization/view', ['id' => $model->organization_id]) . '#tab=tab-department');
$this->pageTitle = $model->name;
?>


<?php $tabView = $this->beginWidget('OTabView', ['id' => 'organization-view']); ?>
    <?php $tabView->beginPage('Info'); ?>
    <div class="content-padded">
        <p><?php echo CHtml::encode($model->description); ?></p>
    </div>
    <?php $this->renderPartial('//role/_list', ['rules' => $model->roles, 'canEdit' => $currentRole->isSuperAdmin || ($currentRole->isAdmin && $currentRole->organization_id == $model->organization_id)]) ?>
    <?php $tabView->endPage(); ?>

    <?php $tabView->beginPage('Event', [], 'tab-event'); ?>
    <?php $this->renderPartial('//event/_list', ['models' => Event::model()->onlyDepartments($model->id)->findAll()]) ?>
    <?php $tabView->endPage(); ?>


    <?php $tabView->beginPage('Task', [], 'tab-task'); ?>
<?php $this->renderPartial('//task/_list', ['models' => Task::model()->onlyDepartment($model->id)->findAll()]) ?>
    <?php $tabView->endPage(); ?>

<?php $this->endWidget() ?>
