<?php
/**
 *
 * @var EventController $this
 * @var EventRecurrence $model
 */
$this->layoutSingle(array('view', 'id' => $model->event_id, 'rid' => $model->id));
$this->pageTitle = 'Add Recurrence'
?>
<div class="content-padded">
    <?php $this->renderPartial('_form_recurrence', ['model' => $model]); ?>
</div>