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


defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

if (YII_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}


/**
 * Class O
 *
 * @method static OrganizzyApplication app()
 */
class O extends Yii {

    /**
     * @param string|array $customConfig
     * @return OrganizzyApplication
     */
    public static function createOrganizzyApplication($customConfig = null)
    {
        $config = self::loadConfigFromCache();
        if (!$config) {
            $config = require(__DIR__ . '/config/main.php');

            if ($customConfig) {
                if (is_array($customConfig)) {
                    $config = CMap::mergeArray($config, $customConfig);
                }
                elseif (file_exists($customConfig)) {
                    $config = CMap::mergeArray($config, require($customConfig));
                }
            }

            if (function_exists('apc_add')) {
                apc_add('Organizzy:config', $config);
            }
        }

        return self::createApplication('OrganizzyApplication' , $config);
    }

    private static function loadConfigFromCache() {
        return null;
        if (function_exists('apc_fetch')) {
            return apc_fetch('Organizzy:config') ?: null;
        }

        return null;
    }
}

/**
 * Class OrganizzyApplication
 *
 * @property AccessRule $accessRule
 * @property boolean $isAjaxRequest
 *
 * @property Mailer $mail
 *
 * @property string $dummyPhoto
 */
class OrganizzyApplication extends CWebApplication {

    /** @var AccessRule */
    private $_accessRule = null;

    public function getDummyPhoto() {
        return $this->getBaseUrl(true) . '/images/dummy_person.gif';
    }

    /**
     * @return AccessRule
     */
    public function getAccessRule() {
        if ($this->_accessRule == null) {
            return $this->_accessRule = new AccessRule($this->user->id);
        }
        return $this->_accessRule;
    }

    public function getIsAjaxRequest() {
        return $this->getClientVersion() != null || $this->request->getIsAjaxRequest();
    }
    
    public function getClientVersion() {
        
        if (preg_match('#OrganizzyMobile/(\S+)#i',  $_SERVER['HTTP_USER_AGENT'], $m) > 0) {
            return $m[1];
        } else {
            return null;
        }
        
    }

    /**
     * @param string $view
     * @param array $params
     * @param int $userId
     */
    public function sendMailToUser($view, $params = [], $userId = null) {
        if (!$userId) {
            $userId = $this->user->id;
        }
        $user = User::model()->findByPk($userId);

        $this->mail->sendTemplate($view, $user->email, $user->name, $params);
    }
}
