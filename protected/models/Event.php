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
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property string $owner_id
 * @property string $organization_id
 * @property string $department_id
 *
 * @property bool $isEditable
 *
 * The followings are the available model relations:
 * @property User $owner
 * @property Organization $organization
 * @property Department $department
 * @property EventRecurrence[] $recurrences
 *
 * @property int $numRecurrence
 */
class Event extends ActiveRecord implements IRoleBasedModel
{
    const TYPE_PERSONAL = 'personal';
    const TYPE_ORGANIZATION = 'organization';
    const TYPE_DEPARTMENT = 'department';
    const TYPE_ADMINS = 'admins';
    const TYPE_CUSTOM = 'custom';


    /**
     * Get users that belong to this event
     *
     * @param int $with_attendance_rid recurrence id. See {@link EventRecurrence.id EventRecurrence.id}
     * @return User[] with attendance
     */
    public function getUsers($with_attendance_rid = null) {
        if ($this->type == self::TYPE_PERSONAL)
            return [$this->owner];

        $criteria = new CDbCriteria();
        $criteria->join .= ' JOIN role ON role.user_id = t.id AND role.organization_id = :oid AND role.status <> \'invited\'';
        $criteria->params[':oid'] = $this->organization_id;
        $criteria->index = 'id';

        if ($this->type == self::TYPE_DEPARTMENT) {
            $criteria->compare('role.department_id', $this->department_id);
        } elseif ($this->type == self::TYPE_ADMINS) {
            $criteria->compare('role.type', [Role::TYPE_ADMIN, Role::TYPE_SUPER_ADMIN]);
        }

        $model = User::model();
        if ($with_attendance_rid) {
            $model->withAttendance($with_attendance_rid);
            $criteria->order = 'attendance.status';
        }
        return $model->findAll($criteria);
    }


    /**
     * @param null $type
     * @return string
     */
    public function getTypeDescription($type = null) {
        static $types = null;
        if ($type == null) $type = [
            self::TYPE_PERSONAL => _t('Personal Event'),
            self::TYPE_DEPARTMENT => _t('Department\'s Event'),
            self::TYPE_ORGANIZATION => _t('Organization\'s Event'),
            self::TYPE_ADMINS => _t('Manager\'s Event'),
        ];
        return $types[$type ?: $this->type];
    }



    /**
     * @param null $user_id
     * @return $this
     */
    public function onlyMine($user_id = null) {
        $user_id = $user_id ?: O::app()->user->id;
        $this->getDbCriteria()->compare('owner_id', $user_id)->compare('type', self::TYPE_PERSONAL);
        return $this;
    }

    /**
     * @return $this
     */
    public function onlyLater() {
        $criteria = $this->getDbCriteria();
        $criteria->join .= ' JOIN event_recurrence er ON er.event_id = t.id';
        $criteria->addCondition('combine_datetime(er.date, er.begin_time) >= NOW()');
        $criteria->select = 't.id, t.title, t.description, min(er.date) as r_date';
        $criteria->group = 't.id, t.title, t.description';
        $criteria->order = 'r_date';
        return $this;
    }

    /**
     * @param $organization_id
     * @param bool $show_admin_events
     * @return $this
     */
    public function onlyOrganization($organization_id, $show_admin_events = false) {
        $criteria = $this->getDbCriteria();
        $criteria->compare('organization_id', $organization_id);
        if ($show_admin_events)
            $criteria->compare('type', [self::TYPE_ORGANIZATION, self::TYPE_ADMINS]);
        else
            $criteria->compare('type', self::TYPE_ORGANIZATION);
        return $this;
    }

    /**
     * @param int $department_id
     * @return $this
     */
    public function onlyDepartments($department_id) {
        $this->getDbCriteria()
            ->compare('type', self::TYPE_DEPARTMENT)
            ->compare('department_id', $department_id);
        return $this;
    }


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'event';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('owner_id, title, description, type', 'required'),
			array('title', 'length', 'max'=>64),
			array('organization_id, department_id', 'safe'),

			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			//array('id, title, description, type, owner_id, organization_id, department_id', 'safe', 'on'=>'search'),
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
			'owner' => array(self::BELONGS_TO, 'User', 'owner_id'),
			'organization' => array(self::BELONGS_TO, 'Organization', 'organization_id'),
			'department' => array(self::BELONGS_TO, 'Department', 'department_id'),
			'recurrences' => array(self::HAS_MANY, 'EventRecurrence', 'event_id',
                                   'index' => 'id', 'order' => 'date, begin_time'),

            'numRecurrence' => [self::STAT, 'EventRecurrence', 'event_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'title' => _t('Title'),
			'description' => _t('Description'),
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Event the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @return array
     */
    public function accessRules()
    {
        $rules = [
            ['action' => '*', 'allow' => $this->owner_id == O::app()->user->id],
        ];

        if ($this->type != self::TYPE_PERSONAL) {
            $rules[] = ['action' => '*', 'organization' => $this->organization_id, 'role' => Role::TYPE_SUPER_ADMIN];

            if ($this->type == self::TYPE_ORGANIZATION) {
                $rules[] = ['action' => 'view', 'organization' => $this->organization_id, 'role' => '*'];
            }
            elseif ($this->type == self::TYPE_ADMINS) {
                $rules[] = ['action' => 'view', 'organization' => $this->organization_id, 'role' => Role::TYPE_ADMIN];
            }
            elseif ($this->type == self::TYPE_DEPARTMENT) {
                $rules[] = ['action' => 'view', 'organization' => $this->organization_id,
                            'department' => $this->department_id,  'role' => '*'];
                $rules[] = ['action' => '*', 'organization' => $this->organization_id,
                            'department' => $this->department_id,  'role' => Role::TYPE_ADMIN];
            }

        }

        return $rules;

    }
}
