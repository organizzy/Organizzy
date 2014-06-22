<?php
/*
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
 * This is the model class for table "activity".
 *
 * The followings are the available columns in table 'activity':
 * @property string $user_id
 * @property string $datetime
 * @property string $organization_id
 * @property string $department_id
 * @property string $recurrence_id
 * @property string $task_id
 * @property string $type
 *
 * @property string $itemType
 * @property string $subType
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Organization $organization
 * @property Department $department
 * @property EventRecurrence $recurrence
 * @property Task $task
 * @property Event $event
 *
 */
class Activity extends ActiveRecord
{
    const TYPE_EVENT = 'event';
    const TYPE_TASK = 'task';

    private $type_split = null;

    /**
     * get title/name of the activity
     * @return string
     */
    public function getTitle() {
        if ($this->isEvent()) {
            return $this->event->title;
        }
        elseif ($this->isTask()) {
            return $this->task->title;
        }
        return '';
    }

    /**
     *
     * @return array|null
     */
    public function getLink() {
        if ($this->isEvent()) {
            return ['/event/view', 'id' => $this->event->id, 'rid' => $this->recurrence_id];
        }
        elseif ($this->isTask()) {
            return ['/task/view', 'id' => $this->task_id];
        } else {
            return null;
        }
    }

    /**
     * @return bool return true if type is event
     */
    public function isEvent() {
        return $this->getItemType() == self::TYPE_EVENT;
    }

    /**
     * @return bool
     */
    public function isTask() {
        return $this->getItemType() == self::TYPE_TASK;
    }

    /**
     * Get activity type
     *
     * @return string return event its type is {@link Event} or task if its type is {@link Task}
     */
    public function getItemType() {
        return $this->getTypePart(0);
    }

    /**
     * @return string depends on item type
     */
    public function getSubType() {
        return $this->getTypePart(1);
    }

    /**
     * get activity type or its subtype. see {@link getItemType} and {@link getSubType}
     * @param int $part 0: item type, 1: sub type
     * @return string
     */
    private function getTypePart($part) {
        if ($this->type_split == null) {
            $this->type_split = explode('-', $this->type);
        }
        return $this->type_split[$part];
    }



    /**
     *
     * @param int $user_id
     * @return $this
     * @scope
     */
    public function onlyMine($user_id) {
        $criteria = $this->getDbCriteria();
        $criteria->compare('user_id', $user_id);
        $criteria->order = 't.datetime';
        //$criteria->together = false;
        return $this;
    }

    /**
     * @param string $date yyy-mm-dd
     * @return $this
     * @scope
     */
    public function onlyForDate($date) {
        $nextDate = date('Y-m-d', strtotime($date) + 86400);
        $this->getDbCriteria()
            ->compare('datetime', '>=' . $date)
            ->compare('datetime', '<' . $nextDate);
        return $this;
    }

    /**
     * @return $this
     */
    public function withDetails() {
        return $this->with([
                'event' => ['select' => 'event.title,event.id'],
                'task' => ['select' => 'task.title'],
                'organization' => ['select' => 'id'],
                'organization.logo' => ['select' => 'logo.url'],
            ]);
    }

    /**
     * @param int $month month index 1: january, 12 december
     * @param int $year full year. ex: 2015
     * @return $this
     */
    public function onlyMonth($month, $year) {
        $criteria =& $this->getDbCriteria();
        $criteria->compare('datetime', sprintf('>=%04d-%02-01 00:00', $year, $month));
        $criteria->compare('datetime', sprintf('<%04d-%02-01 00:00', $year+($month==12?1:0), ($month==12?1:$month+1)));
        return $this;
    }



    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, datetime', 'required'),
            array('type', 'length', 'max'=>32),
            array('organization_id, department_id, recurrence_id, task_id', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('user_id, datetime, organization_id, department_id, recurrence_id, task_id, type', 'safe', 'on'=>'search'),
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
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'organization' => array(self::BELONGS_TO, 'Organization', 'organization_id'),
            'department' => array(self::BELONGS_TO, 'Department', 'department_id'),
            'recurrence' => array(self::BELONGS_TO, 'EventRecurrence', 'recurrence_id'),
            'event' => array(self::BELONGS_TO, 'Event', ['event_id' => 'id'], 'through' => 'recurrence'),
            'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'user_id' => 'User',
            'datetime' => 'Datetime',
            'organization_id' => 'Organization',
            'department_id' => 'Department',
            'recurrence_id' => 'Recurrence',
            'task_id' => 'Task',
            'type' => 'Type',
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'activity';
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Activity the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}