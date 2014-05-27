<?php
/**
 * @var RoleController $this
 * @var Role $model
 *
 */


if ($model->department_id) {
    $this->pageTitle = $model->department->name . ', ' . $model->organization->name;
    $backUrl = ['/department/view', 'id' => $model->department_id];
}
else {
    $this->pageTitle = $model->organization->name;
    $backUrl = ['/organization/view', 'id' => $model->organization_id];
}

//$this->editButton = ['url' => $this->createUrl('/user/view', ['id' => $model->user_id, 'return' => O::app()->request->url]), 'icon' => 'fa-external-link'];

$this->layoutSingle($backUrl);

?>
<div class="content-padded">
    <h2>
        <i class="fa fa-user"></i>
        <?php echo CHtml::encode($model->user->name) ?>

    </h2>
    <?php
    if ($model->user_id != $this->userId) {
        echo CHtml::link('<i class="fa fa-times"></i> Kick', ['kick', 'return' =>  CHtml::normalizeUrl($backUrl)], [
                'class' => 'btn btn-post', 'data-post' => ('oid=' . $model->organization_id . '&uid=' . $model->user_id),
                'data-ask' => O::t('organizzy', 'Kick this user from ') . $model->organization->name . '?',
            ]);
    }
    ?>

    <a href="<?php echo $this->createUrl('/user/view', ['id' => $model->user_id]) . '#return=' . urlencode(O::app()->request->url)  ?>" class="btn btn-primary"><i class="fa fa-external-link"></i> View</a>
    <br /><br /><br />
    <?php $this->renderPartial('_form', ['model' => $model]) ?>

</div>