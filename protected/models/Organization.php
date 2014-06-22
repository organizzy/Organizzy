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
 * This is the model class for table "organization".
 *
 * The followings are the available columns in table 'organization':
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $info
 * @property int $logo_id
 *
 * The followings are the available model relations:
 * @property Photo $logo
 * @property Event[] $events
 * @property Department[] $departments
 * @property Role[] $adminsRole
 *
 *
 * @property Role $role current user role
 */
class Organization extends ActiveRecord implements IRoleBasedModel
{
    /**
     * @param null $user_id
     * @return Role
     */
    public function getCurrentUserRule($user_id = null) {
        if ($user_id == null) {
            $user_id = O::app()->user->id;
        }

        return Role::model()->findByPk(['organization_id' => $this->id, 'user_id' => $user_id]);
    }

    /**
     * @return string return organization name
     */
    public function __toString() {
        return $this->name;
    }



    /**
     * @param null|int $user_id [optional]
     * @param bool $archived
     * @return Organization
     * @scope
     */
    public function onlyMine($user_id = null, $archived = false) {
        if ($user_id == null) {
            $user_id = O::app()->user->id;
        }

        $this->getMetaData()->addRelation('role', [self::HAS_ONE, 'Role', ['organization_id' => 'id'],
                'joinType' => 'JOIN', 'on' => 'user_id = :uid', 'params' => [':uid' => $user_id]]);

        $criteria = $this->with('role')->getDbCriteria();
        $criteria->order = '"role".status, "role".join_time DESC, t.name';

        if (!$archived) {
            $criteria->compare('role.status', '<>' . Role::STATUS_ARCHIVED);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function withLogo() {
        return $this->with(['logo' => ['select' => 'url']]);
    }

    /**
     * @return array access rules
     */
    public function accessRules() {
        return [
            ['action' => AccessRule::VIEW, 'organization' => $this->id, 'role' => '*'],
            ['action' => '*', 'organization' => $this->id, 'role' => [Role::TYPE_SUPER_ADMIN]],
        ];
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            ['name,description', 'required'],

			array('name', 'length', 'max'=>64),
			array('description', 'length', 'max'=>128),
			array('info, create_time, logo_id', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, info, logo, create_time', 'safe', 'on'=>'search'),
		);
	}


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [
            'logo' => [self::HAS_ONE, 'Photo', ['id' => 'logo_id'], 'select' => 'url'],

            'departments' => [self::HAS_MANY, 'Department', 'organization_id',
                'index' => 'id','select' => 'id,name,description'],

            'users' => [self::MANY_MANY, 'User', 'role(organization_id, user_id)', 'index' => 'id',
                'select' => 'id,name', 'order' => '"users_users".department_id, "users_users"."type" DESC, "users".name'],


            'adminsRole' => [self::HAS_MANY, 'Role', 'organization_id',
                'with' => ['user' => ['select' => 'id,name'], 'user.photo' => ['select' => 'url']],
                'select' => 'position,status',
                'order' => '("adminsRole".status <> \'invited\'), "user".name',
                'condition' => 'type = \'' . Role::TYPE_SUPER_ADMIN . '\''],

            'events' => [self::HAS_MANY, 'Event', 'organization_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name' => _t('Name'),
			'description' => _t('Description'),
			'info' => _t('Info'),
		);
	}

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'organization';
    }

    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Organization the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
