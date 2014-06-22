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
 * Class DepartmentController
 *
 */
class DepartmentController extends Controller
{
    /**
     * controller filters
     *
     * @return array
     */
    public function filters() {
        return [
            'postOnly + delete',
        ];
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view',array(
                'model'=>$this->loadModel($id),
            ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate($oid)
    {
        $model=new Department;
        $model->organization_id = $oid;


        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $this->rule->checkCreateAccess($model);
        if(FormHandler::save($model))
        {
            $this->redirect(array('view','id'=>$model->id));
        }

        $this->render('create',array(
                'model'=>$model,
            ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $this->rule->checkUpdateAccess($model);
        FormHandler::saveRedirect($model, ['view', 'id' => $id]);

        $this->render('update',array(
                'model'=>$model,
            ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $model = $this->loadModel($id);
        $org_id = $model->organization_id;
        $this->rule->checkDeleteAccess($model);
        $model->delete();
        O::app()->user->setFlash('success', _t('Department deleted'));
        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/organization/view', 'id' => $org_id));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Department the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model=Department::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,_t('The requested page does not exist.'));
        return $model;
    }
}