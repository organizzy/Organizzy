<?php
/* @var $this Controller */
/* @var $model Role */
/* @var $form CActiveForm */

if ($model->department_id) $back = ['/department/view', 'id' => $model->department_id];
else $back = ['/organization/view', 'id' => $model->organization_id];
$this->layoutSingle();
$this->pageTitle = _t('Invite Member');

?>
<div class="content-padded">
    <p>Invite member to <?php
        if ($model->department_id) echo Tag::b($model->department->name) , ', ';
        echo Tag::b($model->organization->name)
        ?></p>
    <?php $this->renderPartial('_form', ['model' => $model]) ?>
</div>