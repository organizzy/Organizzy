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
            $this->redirect(['/activity/index']);
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
                AjaxHandler::returnScript('O.user.login("' . O::app()->session->sessionID . '");O.navigation.changePage("/activity/index");');
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
        AjaxHandler::returnScript('O.user.logout();O.navigation.changePage("/user/login");');
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
                O::app()->user->setFlash('info', _t('Register success, please login'));
                O::app()->session->add('email', $model->email);
                O::app()->sendMailToUser('user/register', ['model' => $model], $model->id);

                $this->redirect(O::app()->user->loginUrl);
            }
        }

        $this->render('register',array('model'=>$model));
    }

    public function actionForgotPassword() {
        $email = null;
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
            $user = User::model()->findByEmail($email);
            if ($user) {
                O::import('lib.utils.Random', true);
                $newPassword = lib\utils\Random::generate(8, lib\utils\Random::CASE_BOTH, true);
                $user->password = crypt($newPassword);
                $user->save();
                O::app()->mail->sendTemplate('user/reset-password', $user->email, $user->name,
                    ['model' => $user, 'password' => $newPassword]);
                O::app()->user->setFlash('success',
                    O::t('organizzy', 'Your new password was sent to {email}.', ['{email}' => $user->email])
                );
                $this->redirect(['login']);
            }
            else {
                O::app()->user->setFlash('error',
                    O::t('organizzy', 'Email address "{email}" is not found', ['{email}' => $email])
                );
            }
        }

        $this->render('reset-password');
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
        $model = $this->loadModel($this->userId);
        $model->scenario = User::SCENARIO_EDIT_ACCOUNT;
        $model->oldEmail = $model->email;

        if (FormHandler::save($model)) {
            O::app()->user->setFlash('success', _t('Account updated'));
            if ($model->oldEmail != $model->email) {
                O::app()->sendMailToUser('user/activation', ['model' => $model]);
            }
            $this->redirect(['view']);
        }
        $this->render('account', ['model' => $model]);
    }

    public function actionUploadPhoto() {
        $user = $this->loadModel($this->userId);
        if ($user->photo_id)
            $model = Photo::model()->findByPk($user->photo_id);
        else
            $model = new Photo();

        if ($model->handleFileUpload('file', $user)) {
            $result = ['status' => 'OK', 'result' => [
                'normal' => $model->getUrl(),
                'thumb' => $model->getUrl('m'),
            ]];
        } else {
            $result = ['status' => 'ERR', 'error' => $model->getErrors()];
        }
        echo json_encode($result);
    }

    public function actionActivate($resend = false) {
        $model = $this->loadModel($this->userId);

        if (isset($_POST['activation_code'])) {
            if ($model->activation_code == $_POST['activation_code']) {
                $model->status = User::STATUS_ACTIVE;
                if ($model->save()) {
                    O::app()->user->setFlash('success', _t('Your account have been activated'));
                    $this->redirect(['view']);
                }
                else {
                    throw new CHttpException(500, _t('Internal Server Error'));
                }
            } else {
                O::app()->user->setFlash('error', _t('Invalid activation code'));
            }
        }
        elseif ($resend) {
            O::app()->sendMailToUser('user/activation', ['model' => $model]);
            O::app()->user->setFlash('success', _t('The activation code has been sent to {email}', ['{email}' => $model->email]));
        }

        $this->render('activate', ['model' => $model]);
    }

    /**
     * @param $id
     * @return User
     * @throws CHttpException
     */
    private function loadModel($id) {
        $model = User::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, _t('User not found'));
        }
        return $model;
    }
} 