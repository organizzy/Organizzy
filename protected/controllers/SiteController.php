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
 * Class SiteController
 */
class SiteController extends Controller
{
    /**
     * @param string $v client version
     */
    public function actionBoot($v = null) {
        if (O_DEBUG) {
            setcookie('XDEBUG_SESSION', 'XDEBUG', null, O::app()->baseUrl);
        }
        if (O::app()->user->isGuest) {
            $this->redirect(array('/user/login'));

        } else {
            $this->redirect(array('/activity/index'));
        }
    }


    /**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
	    $this->redirect(O::app()->baseUrl . '/index.html', true, 301);
	}

    /**
     * Global setting
     */
    public function actionSetting() {
        $model = new SettingForm();
        FormHandler::saveRedirect($model, ['/activity/index']);
        $this->render('setting', ['model' => $model]);
    }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(O::app()->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}
}
