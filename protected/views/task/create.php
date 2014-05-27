<?php
/* @var TaskController $this */
/* @var Task $model */

$this->layoutSingle($this->getBackUrlByModel($model));
$this->pageTitle = 'Create Task';

?>
<div class="content-padded">
    <?php $this->renderPartial('_form', ['model'=>$model]); ?>
</div>