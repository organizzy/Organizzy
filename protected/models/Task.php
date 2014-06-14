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
 * This is the model class for table "task".
 *
 * The followings are the available columns in table 'task':
 * @property string $id
 * @property string $owner_id
 * @property string $title
 * @property string $description
 * @property string $deadline
 * @property string $status
 * @property string $created
 * @property string $type
 * @property string $organization_id
 * @property string $department_id
 *
 * @property bool done
 *
 *
 * The followings are the available model relations:
 * @property User[] $assignedUsers
 * @property User $owner
 * @property Department $department
 * @property TaskProgress[] $progresses
 */
class Task extends ActiveRecord implements IRoleBasedModel
{
    const TYPE_PERSONAL = 'personal';
    const TYPE_DEPARTMENT = 'department';

    const STATUS_UNDONE = 'undone';
    const STATUS_DONE = 'done';

    const ACTION_UPDATE_PROGRESS = 'progress';

    /** @var int[] list of user id */
    public $assign_to = [];

    /** @var int[] see {@link $assign_to} */
    private $old_assign_to;

    public $date;

    public $time;

    public $useTransaction = true;

    /**
     * set current status
     *
     * @return bool true if task marked as done
     */
    public function getDone() {
        return $this->status == self::STATUS_DONE;
    }

    /**
     * set current status
     *
     * @param bool $done
     */
    public function setDone($done) {
        $this->status = $done ? self::STATUS_DONE : self::STATUS_UNDONE;
    }

    /**
     * only show tasks whose owner is $user_id
     *
     * @param int $user_id
     * @return $this
     */
    public function onlyMine($user_id) {
        $this->getDbCriteria()
            ->compare('type', self::TYPE_PERSONAL)
            ->compare('owner_id', $user_id);
        return $this;
    }

    /**
     * only show tasks which is belong to $department_id
     *
     * @param int $department_id
     * @return $this
     */
    public function onlyDepartment($department_id) {
        $this->getDbCriteria()
            ->compare('type', self::TYPE_DEPARTMENT)
            ->compare('department_id', $department_id);
        return $this;
    }

    /**
     * only show tasks which is not marked as done
     *
     * @return $this
     */
    public function onlyUndone() {
        $this->getDbCriteria()->compare('status', self::STATUS_UNDONE);
        return $this;
    }

    /**
     * @param string $type order type
     * @return $this
     */
    public function orderByDeadline($type = 'ASC') {
        $this->getDbCriteria()->order = 'deadline ' . $type;
        return $this;
    }

    /**
     * show undone tasks first
     *
     * @return $this
     */
    public function orderByDoneStatus() {
        $this->getDbCriteria()->order = 'status, deadline ';
        return $this;
    }



    protected function afterFind() {
        parent::afterFind();
        if ($this->deadline)
            list($this->date, $this->time) = explode(' ', $this->deadline);
        if ($this->type == self::TYPE_DEPARTMENT) {
            foreach($this->assignedUsers as $user) {
                $this->assign_to[$user->id] = 1;
            }
            $this->old_assign_to = $this->assign_to;
        }
    }

    protected function beforeValidate() {
        $this->deadline = $this->date . ' ' . $this->time;
        return parent::beforeValidate();
    }


    protected function afterSave() {
        if ($this->type==self::TYPE_DEPARTMENT) {
            $assign_to = $this->assign_to;
            foreach($assign_to as $id => $v) {
                if ($v == 0) unset($assign_to[$id]);
            }

            $criteria = new CDbCriteria();
            $criteria->compare('task_id', $this->id);
            $criteria->addNotInCondition('user_id', array_keys($assign_to));
            TaskAssign::model()->deleteAll($criteria);

            foreach($assign_to as $id => $v) {
                if ($v == 1 && !isset($this->old_assign_to[$id])) {
                    $assign = new TaskAssign();
                    $assign->user_id = $id;
                    $assign->task_id = $this->id;
                    $assign->save();
                }
            }

            $this->old_assign_to = $this->assign_to;
        }
        parent::afterSave();

    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, date, time, owner_id', 'required'),
			array('title', 'length', 'max'=>64),
            ['date', 'validateDate'],

			array('description, deadline, done, department_id, organization_id, type, assign_to, status', 'safe'),


			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, owner_id, title, description, deadline, done, created, type, department_id', 'safe', 'on'=>'search'),
		);
	}


    public function validateDate() {
        $nowDate = date('Y-m-d');
        $nowTime = date('H:i:s');
        if ($this->date < $nowDate) {
            $this->addError('date', _t('{attribute} can not be past', ['{attribute}' => 'Date']));
        }
        elseif ($this->date == $nowDate && $this->time < $nowTime) {
            $this->addError('begin_time', _t('{attribute} can not be past', ['{attribute}' => 'Begin Time']));
        }
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'assignedUsers' => array(self::MANY_MANY, 'User', 'task_assign(task_id, user_id)', 'select' => 'id,name'),
			'owner' => array(self::BELONGS_TO, 'User', 'owner_id'),
			'department' => array(self::BELONGS_TO, 'Department', 'department_id'),

			'progresses' => array(self::HAS_MANY, 'TaskProgress', 'task_id', 'order' => 'progress DESC'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'owner_id' => 'Owner',
			'title' => 'Title',
			'description' => 'Description',
			'deadline' => 'Deadline',
			'done' => 'Done',
			'created' => 'Created',
			'type' => 'Type',
			'department_id' => 'Department',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Task the static model class
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

            if ($this->type == self::TYPE_DEPARTMENT) {
                $rules[] = ['action' => 'view', 'organization' => $this->organization_id,
                            'department' => $this->department_id,  'role' => '*'];
                $rules[] = ['action' => '*', 'organization' => $this->organization_id,
                            'department' => $this->department_id,  'role' => Role::TYPE_ADMIN];

                $rules[] = ['action' => self::ACTION_UPDATE_PROGRESS, 'allow' => function($item){
                        /** @var Task $item */
                        $count = TaskAssign::model()->countByAttributes(
                            ['task_id'=>$item->id,'user_id'=>O::app()->user->id],
                            ['limit' => 1]
                        );

                        return $count > 0;

                    }];

            }

        }

        return $rules;
    }
}
