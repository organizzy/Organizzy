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

class ProfileEditForm extends CFormModel implements ISavableModel {

    /** @var  int */
    public $user_id;

    /** @var Profile[] */
    private $_profiles;

    private $old_name;


    public $name;

    public static $safeAttributes = array('name', 'aboutMe', 'location', 'description', 'city', 'birth_date', 'phone');

    /**
     * @param string $user_id
     */
    public function __construct($user_id) {
        parent::__construct();
        $this->user_id = $user_id;
        $this->old_name = $this->name = User::model()->findByPk($user_id, ['select' => 'name'])->name;
    }

    private function &getProfiles() {
        if ($this->_profiles == null) {
            $this->_profiles = Profile::model()->findAllByAttributes(
                ['user_id' => $this->user_id], ['index' => 'prop_name']);
        }
        return $this->_profiles;
    }

    public function rules() {
        return [
            ['name', 'required'],
        ];
    }

    public function getSafeAttributeNames() {
        return self::$safeAttributes;
    }

    public function __get($name) {
        if (in_array($name, self::$safeAttributes)) {
            $profiles = $this->getProfiles();
            if (isset($profiles[$name])) {
                return $profiles[$name]->prop_val;
            } else {
                return null;
            }
        }
        else {
            return parent::__get($name);
        }
    }

    public function __set($name, $value) {
        if (in_array($name, self::$safeAttributes)) {
            $this->getProfiles();
            if (! isset($this->_profiles[$name])) {
                $this->_profiles[$name] = new Profile();
                $this->_profiles[$name]->user_id = $this->user_id;
                $this->_profiles[$name]->prop_name = $name;
            }
            $this->_profiles[$name]->prop_val = $value;
            $this->_profiles[$name]->changed = true;
        }
        else {
            parent::__set($name, $value);
        }
    }

    public function save() {

        $transaction = O::app()->db->beginTransaction();
        try {
            if ($this->old_name != $this->name)
                User::model()->updateByPk($this->user_id, ['name' => $this->name]);

            if ($this->_profiles) {
                foreach ($this->_profiles as $profile) {
                    if ($profile->changed) {
                        $profile->save();
                        $profile->changed = false;
                    }
                }
            }
            $transaction->commit();
        }
        catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        return true;
    }
} 