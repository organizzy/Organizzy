<?php
/* @var EventController $this */
/* @var Event $model */
/* @var EventRecurrence $recurrence */
$this->layoutSingle($this->getBackUrlByModel($model));
$this->pageTitle = 'Create Event';

?>
<div class="content-padded">
<?php $this->renderPartial('_form', ['model'=>$model, 'recurrence' => $recurrence]); ?>
</div>