<?php
/**
 *
 * @var TaskController $this
 * @var Task $model
 */


$this->layoutSingle(['view', 'id' => $model->id]);
$this->pageTitle = _t('Edit Task');

?>
<div class="content-padded">
    <?php $this->renderPartial('_form', ['model'=>$model]); ?>
</div>
