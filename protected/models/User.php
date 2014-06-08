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
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property string $register_time
 * @property integer $status
 * @property integer $photo_id
 * @property string $activation_code
 * @property-readonly bool $activated
 *
 * The followings are the available model relations:
 * @property Photo $photo
 * @property Profile[] $profiles
 * @property Organization[] $organizations
 *
 * @property Role[] $roles;
 *
 * @property Event[] $events
 * @property EventRecurrence[] $eventAttendance
 * @property EventTimeOptions[] $eventTimeOptions
 * @property Task[] $tasks
 * @property Task[] $assignedTasks
 * @property TaskProgress[] $taskProgresses
 *
 * @property Profile $aboutMe
 *
 * @property EventAttendance $attendance
 *
 * @method User findByPk($pk)
 */
class User extends ActiveRecord
{

    const STATUS_ACTIVE = 'active';
    const STATUS_NEED_EMAIL_CONFIRMATION = 'email-confirm';
    const STATUS_BLOCKED = 'blocked';

    const SCENARIO_REGISTER = 'register';
    const SCENARIO_EDIT_ACCOUNT = 'edit';

    public $password1, $password2;
    public $old_password;

    public $oldEmail;



    /**
     * return user full name
     *
     * @return string full name
     */
    public function __toString() {
        return $this->name;
    }

    /**
     * return profile value
     *
     * @param string $prop_name
     * @return null|string
     */
    public function getProfile($prop_name) {
        $profiles = $this->profiles;
        if (isset($profiles[$prop_name])) {
            return $profiles[$prop_name]->prop_val;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function getActivated() {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * @param $recurrent_id
     * @return $this
     */
    public function withAttendance($recurrent_id) {
        $this->metaData->addRelation('attendance', [
                self::HAS_ONE, 'EventAttendance', ['user_id' => 'id'], 'on' => 'recurrence_id = :rid',
                'params' => [':rid' => $recurrent_id]
            ]);
        return $this->with('attendance');
    }

    public function defaultScope() {
        return ['with' => 'photo'];
    }

    /**
     * @return bool
     */
    public function beforeSave() {
        if ($this->password1 != '' && $this->scenario == self::SCENARIO_REGISTER || $this->scenario == self::SCENARIO_EDIT_ACCOUNT) {
            $this->password = crypt($this->password1);
        }

        if ($this->scenario == self::SCENARIO_REGISTER ||
            ($this->scenario == self::SCENARIO_EDIT_ACCOUNT && $this->oldEmail != $this->email)
        ) {
            $this->status = self::STATUS_NEED_EMAIL_CONFIRMATION;
            $this->activation_code = rand(1000, 99999);
        }

        return parent::beforeSave();
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            ['email, name', 'required'],
            ['name', 'length', 'max'=>32],
            ['photo_id', 'safe'],

            ['email', 'length', 'max'=>64],
            ['email', 'email'],
            ['email', 'unique'],

            ['password1,password2', 'length', 'max'=>32, 'min' => 4],

            // array('id, email, password, name, photo, register_time, status', 'safe', 'on'=>'search'),

            ['password1, password2', 'required', 'on' => self::SCENARIO_REGISTER],
            ['password1, password2', 'safe', 'on' => self::SCENARIO_EDIT_ACCOUNT],
            ['password2', 'compare',
                'compareAttribute'=>'password1',
                'on' => [self::SCENARIO_REGISTER, self::SCENARIO_EDIT_ACCOUNT]
            ],

            ['old_password', 'checkOldPassword', 'on' => self::SCENARIO_EDIT_ACCOUNT],
        );
    }

    public function checkOldPassword() {
        if ($this->password != crypt($this->old_password, $this->password)) {
            $this->addError('old_password', O::t('organizzy', 'Wrong password'));
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'profiles' => array(self::HAS_MANY, 'Profile', 'user_id', 'index' => 'prop_name'),
            'photo' => array(self::HAS_ONE, 'Photo', ['id' => 'photo_id'], 'select' => 'url'),

            'organizations' => array(self::MANY_MANY, 'Organization', 'role(user_id, organization_id)'),
            'events' => array(self::HAS_MANY, 'Event', 'owner_id'),
            //'eventRecurrences' => array(self::MANY_MANY, 'EventRecurrence', 'event_attendance(user_id, recurrence_id)'),
            //'eventTimeOptions' => array(self::MANY_MANY, 'EventTimeOptions', 'event_vote(user_id, option_id)'),
            //'assignedTasks' => array(self::MANY_MANY, 'Task', 'task_assign(user_id, task_id)'),
            //'tasks' => array(self::HAS_MANY, 'Task', 'owner_id'),
            //'taskProgresses' => array(self::HAS_MANY, 'TaskProgress', 'reporter_id'),

            'roles' => [self::HAS_MANY, 'Role', 'user_id', 'order' => 'join_time DESC'],

            'aboutMe' => array(self::HAS_ONE, 'Profile', ['user_id' => 'id'],
                'select' => 'prop_val', 'on' => 'prop_name = \'aboutMe\'')
        );
    }

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'email' => 'Email',
			'password' => 'Password',
			'name' => 'Name',
			'photo' => 'Photo',
			'register_time' => 'Register Time',
			'status' => 'Status',

            'password1' => 'Password',
            'password2' => 'Confirm Password',
		);
	}

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user';
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
