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
 * @property Photo $logo
 * @property string $create_time
 * @property string $status
 *
 * The followings are the available model relations:
 * @property User[] $users
 * @property Event[] $events
 * @property Department[] $departments
 * @property Role[] $membersRole
 *
 * @property Role $role
 *
 * @property Activity[] $activities;
 * @method Activity[] activities(mixed $args);
 */
class Organization extends ActiveRecord implements IRoleBasedModel
{
    /**
     * @param null|int $user_id [optional]
     * @param bool $archived
     * @return $this
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

    public function withLogo() {
        return $this->with(['logo' => ['select' => 'url']]);
    }

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
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'organization';
	}

    public function __toString() {
        return $this->name;
    }

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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
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


    public function getDepartmentArray() {
        $departments = [];
        foreach ($this->departments as $department) {
            $departments[] = ModelToArray::convert($department, false, ['id','name','description']);
        }
        return $departments;
    }

    public function getAdminArray() {
        return ModelToArray::convert($this->adminsRole, true, [
                'user.id' => 'id',
                'user.name' => 'name',
                'user.photo.url' => 'photo',
                'position'
            ]);
    }

    public function getEventArray() {
        $models = Event::model()->onlyOrganization($this->id, $this->getCurrentUserRule()->isAdmin)->findAll();
        return ModelToArray::convert($models, true, ['id', 'title','description']);
    }


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'users' => array(self::MANY_MANY, 'User', 'role(organization_id, user_id)', 'index' => 'id',
                'select' => 'id,name', 'order' => '"users_users".department_id, "users_users"."type" DESC, "users".name'),

			'events' => array(self::HAS_MANY, 'Event', 'organization_id'),

			'departments' => array(self::HAS_MANY, 'Department', 'organization_id',
                'index' => 'id','select' => 'id,name,description'),
            //'membersRole' => [self::HAS_MANY, 'Role', 'organization_id'],
            'logo' => array(self::HAS_ONE, 'Photo', ['id' => 'logo_id'], 'select' => 'url'),

            //'activities' => [self::HAS_MANY, 'Activity', 'organization_id', 'with' => 'event'],
            'adminsRole' => [self::HAS_MANY, 'Role', 'organization_id',
                'with' => ['user' => ['select' => 'id,name'], 'user.photo' => ['select' => 'url']],
                'select' => 'position,status',
                'order' => '("adminsRole".status <> \'invited\'), "user".name',
                'condition' => 'type = \'' . Role::TYPE_SUPER_ADMIN . '\''],

            // 'role' => [self::HAS_ONE, 'Role', ['organization_id' => 'id'], 'joinType' => 'JOIN'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'description' => 'Description',
			'info' => 'Info',
			'logo' => 'Logo',
			'create_time' => 'Create Time',
			'status' => 'Status',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('info',$this->info,true);
		$criteria->compare('logo',$this->logo,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
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
