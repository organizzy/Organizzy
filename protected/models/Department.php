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
 * This is the model class for table "department".
 *
 * The followings are the available columns in table 'department':
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string $description
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property Event[] $events
 * @property Organization $organization
 * @property Task[] $tasks
 *
 * @property Role[] $roles
 */
class Department extends ActiveRecord implements IRoleBasedModel
{


    public function accessRules() {
        return [
            ['action' => AccessRule::VIEW, 'organization' => $this->organization_id, 'role' => '*'],
            ['action' => '*', 'organization' => $this->organization_id, 'department' => $this->id,
             'role' => Role::TYPE_ADMIN],
            ['action' => '*', 'organization' => $this->organization_id, 'role' => Role::TYPE_SUPER_ADMIN],
        ];
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'department';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('organization_id, name, description', 'required'),
			array('name', 'length', 'max'=>64),
			array('description', 'length', 'max'=>1024),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			//array('id, organization_id, name, description, create_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'events' => array(self::HAS_MANY, 'Event', 'department_id'),
			'organization' => array(self::BELONGS_TO, 'Organization', 'organization_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'department_id'),

            'roles' => [self::HAS_MANY, 'Role', 'department_id',
                'select' => 'type,position,status',
                'with' => ['user' => ['select' => 'id,name'], 'user.photo' => ['select' => 'url']],
                'order' => '("roles".status = \'invited\'), "roles".type DESC, "user".name'],
		);
	}

    public function __toString() {
        return $this->name;
    }


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'organization_id' => 'Organization',
			'name' => 'Name',
			'description' => 'Description',
			'create_time' => 'Create Time',
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
		$criteria->compare('organization_id',$this->organization_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Department the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
