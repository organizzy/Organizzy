<?php
/* @var $this EventController */
/* @var $model EventRecurrence */

$this->layoutSingle(array('view', 'id' => $model->event_id, 'rid' => $model->id));
$this->pageTitle = 'Edit Recurrence'
?>
<div class="content-padded">
    <?php $this->renderPartial('_form_recurrence', array('model'=>$model)); ?>
</div>