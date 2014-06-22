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


if (O_DEBUG) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

Yii::setPathOfAlias('lib', __DIR__ . '/../lib');
Yii::setPathOfAlias('vendor', __DIR__ . '/../vendor');

/**
 * @param string $msg
 * @param array $params
 * @return string
 */
function _t($msg, $params = []) {
    return O::t('organizzy', $msg, $params);
}

/**
 * @param string $msg
 * @param array $params
 */
function _p($msg, $params = []) {
    echo O::t('organizzy', $msg, $params);
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

    /**
     * load application configuration from apc cache
     *
     * @return array|null
     */
    private static function loadConfigFromCache() {
        if (O_DEBUG)
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

    public static $supportedLocale = [
        'en' => 'en_US',
        'en_US' => 'en_US',
        'en_US.UTF-8' => 'en_US',

        'id' => 'id_ID',
        'id_ID' => 'id_ID',
        'in' => 'id_ID',
        'in_ID' => 'id_ID',
        'id_ID.UTF-8' => 'id_ID',
    ];

    /**
     * Organizzy custom initialization
     */
    protected function init() {
        parent::init();
        $this->initLanguage();

        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/\(tz=([^)]+)\)/', $_SERVER['HTTP_USER_AGENT'], $m)) {
            $this->setTimeZone($m[1]);
        }
    }

    private function initLanguage() {
        $lang = null;
        if (!$lang
            && isset($_SERVER['HTTP_USER_AGENT'])
            && preg_match('/\(lang=([a-zA-Z_]+)\)/', $_SERVER['HTTP_USER_AGENT'], $m)
            && isset(self::$supportedLocale[$m[1]]))
        {
                $lang = $m[1];
        }
        if (!$lang && isset($_COOKIE['l']) && isset(self::$supportedLocale[$_COOKIE['l']])) {
            $lang = $_COOKIE['l'];
        }
        if (!$lang && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            foreach(explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $acceptLang) {
                $tmp = explode(';', $acceptLang);
                if (isset(self::$supportedLocale[$tmp[0]])) {
                    $lang = $tmp[0];
                    break;
                }
            }
        }

        if ($lang) {
            $this->setLanguage(self::$supportedLocale[$lang]);
        }
    }

    /** @var AccessRule */
    private $_accessRule = null;

    /**
     * @return string url to dummy photo
     */
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

    /**
     *
     * @return bool
     * todo: remove this method
     */
    public function getIsAjaxRequest() {
        return true; // $this->getClientVersion() != null || $this->request->getIsAjaxRequest();
    }

    /**
     * Get current version of mobile apps used via User-Agent HTTP header
     *
     * @return string version string
     * todo: remove this method
     */
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
