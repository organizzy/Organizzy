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
 * This is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 *
 * @property string $userId current user id
 * @property AccessRule $rule
 */
class Controller extends CController
{
    const LAYOUT_SINGLE = '//layouts/single';
    const LAYOUT_TAB = '//layouts/tab';

	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout = self::LAYOUT_TAB;

	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();

    /**
     * @var string|array url to go when user press back button, only for layout "single"
     */
    public $backUrl = null;

    /**
     * @var bool disable caching on client side
     */
    public $disableCache = false;

    /**
     * @return string
     */
    public function getPageId() {
         return $this->id . '-' . $this->action->id;
    }

    /**
     * redirect user to login page if it have not logged in yet
     */
    public function init() {
        if ($this->id !== 'user' && O::app()->user->isGuest) {
            $this->redirect(O::app()->user->loginUrl);
        }
    }

    /**
     * @param CAction $action
     * @return bool
     */
    protected function beforeAction($action) {
        if ($action->id == 'update') {
            $this->disableCache = true;
        }
    }

    /**
     * @param null $backUrl
     * @return $this
     */
    public function layoutSingle($backUrl = null) {
        $this->layout = '//layouts/single';
        if ($backUrl) {
            if (is_array($backUrl)) {
                $route = $backUrl[0];
                unset($backUrl[0]);
                $this->backUrl = $this->createUrl($route, $backUrl);
            } else
                $this->backUrl = $backUrl;
        }
        return $this;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title) {
        $this->pageTitle = $title;
        return $this;
    }

    /**
     * @return int
     * @see userId
     */
    public function getUserId() {
        return O::app()->getUser()->getId();
    }

    /**
     * @return AccessRule
     */
    public function getRule() {
        return O::app()->getAccessRule();
    }
}