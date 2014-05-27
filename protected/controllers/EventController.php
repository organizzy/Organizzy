<?php
/**
 * Organizzy
 * Copyright (C) 2014 Organizzy Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class EventController
 */
class EventController extends Controller
{

    /**
     * controller filters
     *
     * @return array
     */
    public function filters() {
        return [
            'postOnly + delete, deleteRecurrence, confirm',
        ];
    }

    /**
     * Lists all models.
     */
    public function actionIndex($all = false)
    {
        $model = Event::model()->onlyMine();
        if (! $all) $model->onlyLater();

        $this->render('index',['models' => $model->findAll(), 'all' => $all]);
    }


    /**
     * Displays a particular model.
     *
     * @param integer $id the ID of the model to be displayed
     * @param null $rid
     * @throws CHttpException
     */
	public function actionView($id, $rid = null)
	{
        $model = $this->loadModel($id);
        $this->rule->checkViewAccess($model);

        /** @var EventRecurrence $recurrence */
        $recurrence = null;
        if ($rid) {
            // TODO: ga efisien, bro
            $recurrence = EventRecurrence::model()->findByPk($rid);
            if ($recurrence->event_id != $id) {
                throw new CHttpException(404);
            }
        }
        if (! $recurrence && $model->numRecurrence > 1) {
            foreach ($model->recurrences as $r) {
                if ($r->date >= date('Y-m-h')) {
                    $recurrence = $r;
                    break;
                }
            }
        }
        if (! $recurrence) {
            $recurrences = $model->recurrences;
            reset($recurrences);
            $recurrence = current($recurrences);
        }
		$this->render('view',array('model'=>$model, 'recurrence' => $recurrence));
	}


	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($type = Event::TYPE_PERSONAL, $oid = null, $did = null)
	{
		$event=new Event;
        $recurrence = new EventRecurrence();

        $event->owner_id = $this->userId;
        $event->type = $type;
        $event->organization_id = $oid;
        $event->department_id = $did;

        $this->rule->checkCreateAccess($event);

        $recurrence->date = date('Y-m-d');

        $transaction = O::app()->db->beginTransaction();
        try {
            if(FormHandler::save($event))
            {
                $recurrence->event_id = $event->id;
                if (FormHandler::save($recurrence)) {
                    $transaction->commit();

                    $this->redirect(array('view','id'=>$event->id));
                }
            }

        } catch (Exception $e) {
            $transaction->rollback();
        }


		$this->render('create',['model'=>$event, 'recurrence' => $recurrence]);
	}


	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
        $this->rule->checkUpdateAccess($model);


        FormHandler::saveRedirect($model, ['view', 'id' => $id]);
		$this->render('update', ['model'=>$model]);
	}

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     *
     * @param integer $id the ID of the model to be deleted
     * @throws CHttpException
     */
	public function actionDelete($id)
	{
		$model = $this->loadModel($id); //->delete();
        $this->rule->checkDeleteAccess($model);

        //if (isset($_POST['confirm'])) {
            $backUrl = $this->getBackUrlByModel($model);
            if ($model->delete()) {
                O::app()->user->setFlash('success', 'Event has been deleted');
            } else {
                O::app()->user->setFlash('error', 'Failed to delete event');
            }

            $this->redirect($backUrl);
        //}
	}

    /**
     * Add new recurrence to current event
     *
     * @param $id
     */
    public function actionAddRecurrence($id) {
        $event = $this->loadModel($id);
        $this->rule->canUpdate($event);

        $model = new EventRecurrence;
        $model->event_id = $id;
        if (FormHandler::save($model)) {
            $this->redirect(['view', 'id' => $id, 'rid' => $model->id]);
        }
        $this->render('add-recurrence', ['model' => $model]);
    }

    public function actionEditRecurrence($id, $rid) {
        $event = $this->loadModel($id);
        $this->rule->canUpdate($event);

        $model = EventRecurrence::model()->findByPk($rid);
        FormHandler::saveRedirect($model, ['view', 'id' => $id, 'rid' => $rid]);
        $this->disableCache = true;
        $this->render('edit-recurrence', ['model' => $model]);
    }

    public function actionDeleteRecurrence($id, $rid) {
        $event = $this->loadModel($id);
        $this->rule->canUpdate($event);


        //if (isset($_POST['confirm'])) {
            if (EventRecurrence::model()->deleteByPk($rid)) {
                O::app()->user->setFlash('success', 'Recurrence has been deleted');
            } else {
                O::app()->user->setFlash('error', 'Failed to delete recurrence');
            }
        //}
        $this->redirect(['view', 'id' => $id]);
    }


    /**
     * Confirm event attendance
     *
     * @param int $id event id
     * @param int $rid recurrence id
     * @throws CHttpException
     */
    public function actionConfirm($id, $rid) {
        $event = $this->loadModel($id);
        if ($event->type == Event::TYPE_PERSONAL)
            throw new CHttpException(400, 'Invalid event type');
        // todo: check if user is invited


        $model = EventAttendance::model()->findByPk(['recurrence_id' => $rid, 'user_id' => $this->userId]);
        if (!$model) {
            $model = new EventAttendance();
            $model->recurrence_id = $rid;
            $model->user_id = $this->userId;
        }

        if (FormHandler::save($model)) {
            $this->redirect(['view', 'id' => $id, 'rid' => $rid]);
        }
    }


    public function actionSetupVote($id, $rid) {
        $event=$this->loadModel($id);
        $this->rule->checkUpdateAccess($event);

        $model = new EventSetupVoteForm();
        $model->event_id = $id;
        $model->recurrence_id = $rid;

        $this->render('setupVote', ['model' => $model]);
    }

    /**
     * @param Event $model
     * @return array
     */
    public function getBackUrlByModel($model) {
        switch($model->type) {
            case Event::TYPE_ORGANIZATION:
            case Event::TYPE_ADMINS:
                $backUrl = CHtml::normalizeUrl(['/organization/view', 'id' => $model->organization_id]) . '#tab=tab-event';
                break;

            case Event::TYPE_DEPARTMENT:
                $backUrl = CHtml::normalizeUrl(['/department/view', 'id' => $model->department_id]) . '#tab=tab-event';
                break;

            default:
                $backUrl = ['index'];
        }
        return $backUrl;
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Event the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Event::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Event $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='event-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
