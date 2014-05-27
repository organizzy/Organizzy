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
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_attendance';
    }

    public static  function getStatusStrings($status = null) {
        $strings = null;
        if (!$strings) $strings = [
            self::STATUS_UNKNOWN => O::t('organizzy', 'Not Responded'),
            self::STATUS_ATTEND => O::t('organizzy', 'Attend'),
            self::STATUS_NOT_ATTEND => O::t('organizzy', 'Not Attend'),
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
            'recurrence_id' => 'Recurrence',
            'user_id' => 'User',
            'status' => 'Status',
            'comment' => 'Comment',
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

        $criteria->compare('recurrence_id',$this->recurrence_id,true);
        $criteria->compare('user_id',$this->user_id,true);
        $criteria->compare('status',$this->status,true);
        $criteria->compare('comment',$this->comment,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
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