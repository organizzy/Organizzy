<?php
/* @var $this Controller */
/* @var $model Role */
/* @var $form CActiveForm */

$this->layoutSingle();
$this->pageTitle = 'Invite Member';

?>
<div class="content-padded">
    <p>Invite member to <?php
        if ($model->department_id) echo Tag::b($model->department->name) , ', ';
        echo Tag::b($model->organization->name)
        ?></p>
    <?php $this->renderPartial('_form', ['model' => $model]) ?>
</div>