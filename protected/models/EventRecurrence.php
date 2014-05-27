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
 * This is the model class for table "event_recurrence".
 *
 * The followings are the available columns in table 'event_recurrence':
 * @property string $id
 * @property string $event_id
 * @property string $vote_status
 * @property string $date
 * @property string $begin_time
 * @property string $end_time
 * @property string $place
 *
 * The followings are the available model relations:
 * @property User[] $users
 * @property Event $event
 * @property EventTimeOptions[] $eventTimeOptions
 */
class EventRecurrence extends ActiveRecord
{

    const VOTE_CLOSED = 'closed';
    const VOTE_OPEN = 'open';
    const VOTE_VOTED = 'voted';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'event_recurrence';
	}

    public function __toString() {
        return O::app()->dateFormatter->formatDateTime($this->date, 'medium', false);
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_id, date, begin_time, ', 'required'),
			//array('vote_status', 'numerical', 'integerOnly'=>true),
			array('end_time, place', 'safe'),

            ['date', 'validateDate'],

            ['end_time', 'compare', 'operator' => '>', 'compareAttribute' => 'begin_time', 'allowEmpty' => true],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, event_id, vote_status, date, begin_time, end_time', 'safe', 'on'=>'search'),
		);
	}

    public function validateDate($attribute) {
        $nowDate = date('Y-m-d');
        $nowTime = date('H:i:s');
        if ($this->date < $nowDate) {
            $this->addError('date', O::t('organizzy', '{attribute} can not be past', ['{attribute}' => 'Date']));
        }
        elseif ($this->date == $nowDate && $this->begin_time < $nowTime) {
            $this->addError('begin_time', O::t('organizzy', '{attribute} can not be past', ['{attribute}' => 'Begin Time']));
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
			'users' => array(self::MANY_MANY, 'User', 'event_attendance(recurrence_id, user_id)'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'eventTimeOptions' => array(self::HAS_MANY, 'EventTimeOptions', 'recurrence_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event_id' => 'Event',
			'vote_status' => 'Vote Status',
			'date' => 'Date',
			'begin_time' => 'Begin Time',
			'end_time' => 'End Time',
		);
	}


    public function beforeSave() {
        if ($this->end_time == '') {
            $this->end_time = null;
        }
        return parent::beforeSave();
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
		$criteria->compare('event_id',$this->event_id,true);
		$criteria->compare('vote_status',$this->vote_status);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('begin_time',$this->begin_time,true);
		$criteria->compare('end_time',$this->end_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EventRecurrence the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
