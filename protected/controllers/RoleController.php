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
 * Class RoleController
 */
class RoleController extends Controller {

    /**
     * Update user role
     *
     * @param int $id organization id
     * @param int $uid user id
     */
    public function actionUpdate($id, $uid) {
        /** @var Role $model */
        $model = Role::model()->with([
                'organization' => ['select' => 'id,name'],
                'department' => ['select' => 'id,name'],
                'user' => ['select' => 'id,name'],
                //'user.photo' => ['select' => 'url'],
            ])->findByPk(['organization_id' => $id, 'user_id' => $uid]);

        $model->is_admin = $model->type == Role::TYPE_ADMIN;

        if (FormHandler::save($model)) {
            if ($model->department_id) {
                $this->redirect(['/department/view', 'id' => $model->department_id]);
            } else {
                $this->redirect(['/organization/view', 'id' => $model->organization_id]);
            }
        }
        $this->render('update', ['model' => $model]);
    }

    /**
     *
     * invite member by email
     *
     * @param $id organization id
     * @param null $department
     */
    public function actionInvite($id, $department = null) {
        $model = new Role('invite');
        $model->organization_id = $id;
        $model->department_id = $department;

        if ($department) {
            $this->backUrl = $this->createUrl('//department/view', ['id' => $department]);
        }
        else {
            $this->backUrl = $this->createUrl('//organization/view', ['id' => $id]);
            $model->type = Role::TYPE_SUPER_ADMIN;
        }

        if(FormHandler::save($model))
        {
            O::app()->user->setFlash('success', _t('Invitation sent'));
            $this->redirect($this->backUrl);
        }

        $this->render('invite',array('model'=>$model));
    }

    /**
     * manage member of organization or department
     *
     * @param int $id organization id
     * @param int $department department id
     */
    public function actionManage($id, $department = null) {
        $model = new ManageMemberForm();
        $model->organization_id = $id;
        $model->department_id = $department;


        if ($department) {
            $this->backUrl = $this->createUrl('//department/view', ['id' => $department]);
        }
        else {
            $this->backUrl = $this->createUrl('//organization/view', ['id' => $id]);
        }

        if(FormHandler::save($model))
        {
            O::app()->user->setFlash('success', _t('Action done'));
            $this->redirect($this->backUrl);
        }

        $this->render('manage',array('model'=>$model));
    }

    /**
     *
     */
    public function actionKick() {
        if (isset($_POST['oid'], $_POST['uid'])) {
            // TODO: check access
            if (Role::model()->deleteByPk(['organization_id' => $_POST['oid'], 'user_id' => $_POST['uid']])) {
                O::app()->user->setFlash('success', 'User has been kicked');
                $this->redirect(isset($_GET['return']) ? $_GET['return'] : ['/organization/view', 'id' => $_POST['oid']]);
            }
            else {
                O::app()->user->setFlash('error', 'Error when removing user from organization');
                $this->redirect(['update', 'id' => $_POST['oid'], 'uid' => $_POST['uid']]);
            }

        }
    }
} 