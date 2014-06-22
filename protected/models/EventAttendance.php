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
 * This is the model class for table "event_attendance".
 *
 * The followings are the available columns in table 'event_attendance':
 * @property string $recurrence_id
 * @property string $user_id
 * @property string $status
 * @property string $comment
 */
class EventAttendance extends ActiveRecord
{

    const STATUS_UNKNOWN = 'unknown';
    const STATUS_ATTEND = 'attend';
    const STATUS_NOT_ATTEND = 'not-attend';

    /**
     * translate status
     *
     * @param null|string $status
     * @return string|null
     */
    public static  function getStatusStrings($status = null) {
        $strings = null;
        if (!$strings) $strings = [
            self::STATUS_UNKNOWN => _t('Not Responded'),
            self::STATUS_ATTEND => _t('Attend'),
            self::STATUS_NOT_ATTEND => _t('Not Attend'),
        ];
        return $status ? $strings[$status] : $strings;
    }



    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('recurrence_id, user_id', 'required'),
            array('status, comment', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('recurrence_id, user_id, status, comment', 'safe', 'on'=>'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'comment' => _t('Comment'),
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_attendance';
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EventAttendance the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}