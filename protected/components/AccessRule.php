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
 * Class for checking if user can access an item
 *
 */
class AccessRule {
    const VIEW = 'view';
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /** @var int user id */
    private $user_id;

    /** @var string[]  */
    private $roles;

    /** @var  int[] */
    private $department_maps;

    /** @var string  */
    private $cache_name;

    /** @var bool  */
    private $cache_loaded = false;

    /**
     * @param int $user_id
     */
    public function __construct($user_id) {
        $this->user_id = $user_id;
        $this->cache_name = 'Organizzy:userRoles:' . $user_id;
    }


    /**
     * check if user can create $item
     *
     * @param IRoleBasedModel $item
     * @param bool $throw
     * @return bool
     */
    public function checkCreateAccess(IRoleBasedModel $item, $throw = true) {
        return $this->checkAccess(self::CREATE, $item, $throw);
    }

    public function checkViewAccess(IRoleBasedModel $item, $throw = true) {
        return $this->checkAccess(self::VIEW, $item, $throw);
    }

    public function checkUpdateAccess(IRoleBasedModel $item, $throw = true) {
        return $this->checkAccess(self::UPDATE, $item, $throw);
    }


    public function checkDeleteAccess(IRoleBasedModel $item, $throw = true) {
        return $this->checkAccess(self::DELETE, $item, $throw);
    }

    public function canUpdate(IRoleBasedModel $item) {
        return $this->can(self::UPDATE, $item);
    }

    /**
     * @param string $action
     * @param IRoleBasedModel $item
     * @throws CException
     * @return bool
     */
    public function can($action, IRoleBasedModel $item) {
        foreach($item->accessRules() as $rule) {
            if (! isset($rule['action']))
                throw new CException('Wrong access rule');

            if ((is_array($rule['action']) && in_array($action, $rule['action'])) ||
                $rule['action'] == '*' || $rule['action'] == $action)
            {
                if (isset($rule['allow'])) {
                    if (is_bool($rule['allow']) && $rule['allow'])
                        return true;
                    elseif (is_callable($rule['allow']) && call_user_func($rule['allow'], $item, $action))
                        return true;
                }

                if (isset($rule['deny']) && $rule['deny']) {
                    return false;
                }

                if (isset($rule['organization'], $rule['role'])
                    && ($role = $this->getRoleByOrganization($rule['organization']))
                ) {
                    $roleType = $rule['role'];
                    if (
                        (!isset($rule['department']) || $role->department_id == $rule['department']) &&
                        ((is_array($roleType) && in_array($role->type, $roleType)) ||
                            $roleType == '*' || $roleType == $role->type)
                    ) {
                        return true;
                    }
                }

            }
        }

        return false;
    }

    /**
     * @param $action
     * @param IRoleBasedModel $item
     * @param bool $throwError
     * @return bool
     * @throws CHttpException
     */
    public function checkAccess($action, IRoleBasedModel $item, $throwError = true) {
        if ($this->can($action, $item)) {
            return true;
        } elseif ($throwError) {
            throw new CHttpException(403, 'Access Denied');
        } else {
            return false;
        }
    }

    /**
     * get {@link Role user role} by organization id
     *
     * @param int $id organization id
     * @return null|Role
     * @see Role
     */
    private function getRoleByOrganization($id) {
        if (($role = $this->getRoleFromCache($id)) === null) {
            $role = Role::model()->findFor($id, $this->user_id);
            if ($role == null) {
                return null;
            }
            $this->saveCache($role);
            return $role;
        }
        return $role;
    }

    /**
     * @param $organizationI_id
     * @return null|Role
     */
    private function getRoleFromCache($organizationI_id) {
        if (!$this->cache_loaded) {
            $this->roles = O::app()->cache->get($this->cache_name) ?: [];
            $this->cache_loaded = true;
        }
        if (isset($this->roles[$organizationI_id])) {
            $arg = $this->roles[$organizationI_id];
            $role = new Role();
            $role->attributes = [
                'department_id' => $arg['d'],
                'type' => $arg['t'],
            ];
            return $role;
        }
        return null;
    }

    /**
     * @param Role $role
     */
    private function saveCache(Role $role) {
        $update = false;
        if (!isset($this->roles[$role->organization_id])) {
            $this->roles[$role->organization_id] = ['d' => $role->department_id, 't' => $role->type];
            $update = true;
        }

        if ($update) {
            O::app()->cache->set($this->cache_name, $this->roles, 60);
        }

    }


}