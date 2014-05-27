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

class TaskController extends Controller {

    public function filters() {
        return [
            'postOnly + delete, changeStatus',
        ];
    }

    public function actionIndex($all = false) {
        $model = Task::model()->onlyMine($this->userId);
        if (! $all) {
            $model->onlyUndone()->orderByDeadline();
        } else {
            $model->orderByDoneStatus();
        }
        $this->render('index', ['models' => $model->findAll(), 'all' => $all]);
    }

    public function actionView($id) {
        $model = $this->loadModel($id);
        $this->rule->checkViewAccess($model);
        $this->render('view', ['model' => $model]);

    }

    public function actionCreate($type = Task::TYPE_PERSONAL, $oid = null, $did = null) {
        $model = new Task('create');
        $model->type = $type;
        $model->organization_id = $oid;
        $model->department_id = $did;
        $model->owner_id = $this->userId;
        $model->done = false;

        $this->rule->checkCreateAccess($model);

        if (FormHandler::save($model)) {
            $this->redirect(['view', 'id' => $model->id]);
        }
        $this->render('create', ['model' => $model]);
    }


    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $this->rule->checkUpdateAccess($model);
        FormHandler::saveRedirect($model, ['view', 'id' => $id]);

        $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id) {
        $model = $this->loadModel($id);
        $this->rule->checkDeleteAccess($model);

        if (isset($_POST['confirm'])) {
            $backUrl = $this->getBackUrlByModel($model);
            if ($model->delete()) {
                O::app()->user->setFlash('success', 'Task has been deleted');
            } else {
                O::app()->user->setFlash('error', 'Task to delete event');
            }

            $this->redirect($backUrl);
        }

        throw new CHttpException(405);
    }

    public function actionChangeStatus($id) {
        if (isset($_POST['done'])) {
            $model = $this->loadModel($id);
            $this->rule->checkUpdateAccess($model);

            Task::model()->updateByPk($id, ['status' => $_POST['done'] == 1 ? Task::STATUS_DONE : Task::STATUS_UNDONE]);
            //$this->redirect(['view', 'id' => $id]);

            AjaxHandler::returnScript('$("#cb-change-status").prop("checked",' .
                ($_POST['done'] == 1 ? 'true' : 'false') . ');');
        }
        throw new CHttpException(403);
    }

    public function actionAddProgress($id) {
        $task = $this->loadModel($id);
        $this->rule->checkAccess(Task::ACTION_UPDATE_PROGRESS, $task);

        $model = new TaskProgress();
        $model->reporter_id = $this->userId;
        $model->task_id = $id;

        FormHandler::saveRedirect($model, ['view', 'id' => $id]);
        throw new CHttpException(403);
    }


    /**
     * @param $id
     * @return Task
     * @throws CHttpException
     */
    private function loadModel($id) {
        $model = Task::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404);
        }
        return $model;
    }

    public function getBackUrlByModel(Task $model) {
        if ($model->type == Task::TYPE_DEPARTMENT)
            return CHtml::normalizeUrl(['/department/view', 'id' => $model->department_id]) . '#tab=tab-task';
        else
            return ['index'];
    }
} 