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
 * Class OrganizationController
 */
class OrganizationController extends Controller
{

    /**
     * Lists all models.
     */
    public function actionIndex($all = false)
    {
        $organizations = Organization::model()->onlyMine($this->userId, $all)->withLogo()->findAll();
        $this->render('index', ['organizations'=>$organizations, 'all' => $all]);
    }

    /**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
        $model = $this->loadModel($id);
        $this->rule->checkViewAccess($model);

		$this->render('view', array('model'=>$model));
	}

	/**
	 * Creates a new model.
	 * Everyone can create
	 */
	public function actionCreate()
	{
		$model=new Organization;
        $model->scenario = 'create';
        $role = new Role();
        $transaction = O::app()->db->beginTransaction();
        try {
            if(FormHandler::save($model)){
                $role->user_id = $this->userId;
                $role->type = Role::TYPE_SUPER_ADMIN;
                $role->organization_id = $model->id;
                $role->status = Role::STATUS_JOINT;

                if (FormHandler::save($role)) {
                    $transaction->commit();
                    $this->redirect(array('view','id'=>$model->id));
                }
            }
        }
        catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }

		$this->render('create',array('model'=>$model));
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

        FormHandler::saveRedirectView($model);

		$this->render('update', ['model'=>$model]);
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        $model = $this->loadModel($id);
        $this->rule->checkDeleteAccess($model);

        if (isset($_POST['confirm'])) {
            $model->delete();
            O::app()->user->setFlash('success', 'Organization has been deleted');
            $this->redirect(array('index'));
        }

        $this->render('delete', array('model' => $model));
	}


    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionConfirmInvitation($id) {
        if (isset($_POST['confirm'])) {
            /** @var Role $joinInfo */
            Role::model()->updateByPk(
                ['user_id' => $this->userId, 'organization_id' => $id],
                ['status' => Role::STATUS_JOINT]
            );
            O::app()->user->setFlash('success', 'You have joint this organization');
            $this->redirect(array('view', 'id' => $id));
        }
        throw new CHttpException(403, 'Access Denied');
    }


    /**
     *
     * leave current organization
     *
     * @param int $id organization id
     */
    public function actionLeave($id) {
        $otherAdminCount = Role::model()->count([
                'condition' => 'organization_id = :oid AND user_id <> :uid AND type != :type',
                'limit' => 1,
                'params' => [':oid' => $id, ':uid' => $this->userId, ':type' => Role::TYPE_SUPER_ADMIN],
            ]);
        $lastAdmin = $otherAdminCount < 1;

        if(! $lastAdmin && isset($_POST['leave']))
        {
            Role::model()->deleteByPk(['user_id' => $this->userId, 'organization_id' => $id]);
            O::app()->user->setFlash('success', 'Leaving successful');
            $this->redirect(array('/organization'));
        }

        $this->render('leave', array('model' => $this->loadModel($id), 'lastAdmin' => $lastAdmin));
    }

    /**
     * upload photo
     *
     * @param int $id organization id
     */
    public function actionUploadPhoto($id) {
        $org = $this->loadModel($id);
        $this->rule->checkUpdateAccess($org);

        if ($org->logo_id)
            $model = Photo::model()->findByPk($org->logo_id);
        else
            $model = new Photo();

        if (FormHandler::save($model)) {
            if ($org->logo_id != $model->id)
                Organization::model()->updateByPk($id, ['logo_id' => $model->id]);
            $org->invalidateCache();
            //$this->redirect(['view', 'id' => $id]);
            AjaxHandler::returnScript('$("#profile-photo").css({backgroundImage:"url(' . $model->getUrl('m') . ')"})');
        }
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Organization the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Organization::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

}
