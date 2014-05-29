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
 * Class O
 *
 * @method static OrganizzyApplication app()
 */
class O extends Yii {
    public static function createOrganizzyApplication($config=null)
    {
        return self::createApplication('OrganizzyApplication',$config);
    }
}

/**
 * Class OrganizzyApplication
 *
 * @property AccessRule $accessRule
 * @property boolean $isAjaxRequest
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
}
