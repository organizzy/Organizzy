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
 * Class ManageMemberForm
 *
 * @property Organization $organization
 * @property Role[] $roles;
 */
class ManageMemberForm extends CFormModel implements ISavableModel {

    const ACTION_KICK = 'kick';
    const ACTION_PROMOTE = 'promote';
    const ACTION_DEMOTE = 'demote';


    /** @var  int */
    public $organization_id;

    /** @var  int */
    public $department_id;

    /** @var  int[] */
    public $users_id;

    public $action;

    /** @var  Organization */
    private $_organization = null;


    /** @var Role[] */
    private $_roles = false;

    /**
     * @return array action_name => action_description
     */
    public function getPossibleActions() {
        return [
            self::ACTION_KICK => _t('Kick'),
            self::ACTION_PROMOTE => _t('Promote to Admin'),
            self::ACTION_DEMOTE => _t('Demote from Admin'),
        ];
    }

    /**
     * get organization instance
     *
     * @return Organization
     */
    public function getOrganization() {
        if ($this->_organization == null) {
            return $this->_organization = Organization::model()->findByPk($this->organization_id);
        }
        return $this->_organization;
    }

    /**
     * @return Role[]
     */
    public function getRoles() {
        if ($this->_roles === false) {
            $this->_roles = Role::model()->exceptMe()->with('user')->findAllByAttributes(
                ['organization_id' => $this->organization_id, 'department_id' => $this->department_id],
                ['order' => 't."type" DESC, "user"."name"']
            );
        }
        return $this->_roles ?: [];
    }

    public function rules() {
        return [
            ['users_id, action', 'safe']
        ];
    }

    public function save() {
        switch ($this->action) {
            case self::ACTION_KICK:
                return $this->kick();

            case self::ACTION_PROMOTE:
            case self::ACTION_DEMOTE:
            default:
                return false;
        }
    }

    protected  function kick() {
        $cr = new CDbCriteria();
        $cr->addInCondition('user_id', $this->users_id);
        $cr->compare('organization_id', $this->organization_id);

        return Role::model()->deleteAll($cr);
    }

    public function promote() {
        //Role::model()->updateAll()
    }
} 