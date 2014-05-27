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

class UserController extends Controller {
    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        if (!O::app()->user->isGuest ){
            $this->redirect(O::app()->user->returnUrl);
        }

        $model=new LoginForm;
        $model->username = O::app()->session->get('email');
        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            O::app()->session->add('email', $model->username);

            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login()) {
                AjaxHandler::returnScript('localStorage.clear();localStorage.setItem("sessionId","' . O::app()->session->sessionID . '");O.navigation.changePage("/activity/index");');
            }
                //$this->redirect(O::app()->user->returnUrl);
        }
        // display the login form

        $this->render('login',array('model'=>$model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        O::app()->user->logout();
        $this->redirect(O::app()->user->loginUrl);
    }

    /**
     *
     */
    public function actionRegister() {
        //$model = new UserRegisterForm();
        $model = new User('register');

        if(isset($_POST['User']))
        {
            $model->attributes=$_POST['User'];

            if($model->validate() && $model->save()) {
                O::app()->user->setFlash('info', O::t('organizzy', 'Register success, please login'));
                O::app()->session->add('email', $model->email);
                $this->redirect(O::app()->user->loginUrl);
            }
        }

        $this->render('register',array('model'=>$model));
    }

    public function actionView($id = null) {
        if (!$id) $id = $this->userId;
        $user = $this->loadModel($id);
        $this->render('view', array('user' => $user));
    }

    public function actionEdit() {
        $model = new ProfileEditForm($this->userId);

        if (FormHandler::save($model)) {
            $return = isset($_GET['return']) ? $_GET['return'] : array('view', 'id' => $this->userId);
            $this->redirect($return);
        }
        $this->render('edit', array('model' => $model));
    }

    public function actionAccount() {
        $user = $this->loadModel($this->userId);
        $user->scenario = User::SCENARIO_EDIT_ACCOUNT;

        if (FormHandler::save($user)) {
            O::app()->user->setFlash('success', 'Account updated');
            $this->redirect(['view']);
        }
        $this->render('account', ['model' => $user]);
    }

    public function actionUploadPhoto() {
        $user = $this->loadModel($this->userId);

        if ($user->photo_id)
            $model = Photo::model()->findByPk($user->photo_id);
        else
            $model = new Photo();

        if (FormHandler::save($model)) {
            if ($user->photo_id != $model->id)
                User::model()->updateByPk($this->userId, ['photo_id' => $model->id]);

            $user->invalidateCache();
            $this->redirect(['view', 'id' => $this->userId]);
        }
    }

    /**
     * @param $id
     * @return User
     * @throws CHttpException
     */
    private function loadModel($id) {
        $model = User::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, 'Profile not found');
        }
        return $model;
    }
} 