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
 * This is the model class for table "event_time_options".
 *
 * The followings are the available columns in table 'event_time_options':
 * @property string $id
 * @property string $recurrence_id
 * @property string $date
 * @property string $begin_time
 * @property string $end_time
 *
 * The followings are the available model relations:
 * @property EventRecurrence $recurrence
 * @property User[] $users
 */
class EventTimeOptions extends ActiveRecord
{
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('recurrence_id, date, begin_time', 'required'),
			array('end_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, recurrence_id, date, begin_time, end_time', 'safe', 'on'=>'search'),
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
			'recurrence' => array(self::BELONGS_TO, 'EventRecurrence', 'recurrence_id'),
			'users' => array(self::MANY_MANY, 'User', 'event_vote(option_id, user_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'recurrence_id' => 'Recurrence',
			'date' => 'Date',
			'begin_time' => 'Begin Time',
			'end_time' => 'End Time',
		);
	}

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_time_options';
    }

    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EventTimeOptions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
