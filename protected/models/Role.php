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
 * This is the model class for table "role".
 *
 * The followings are the available columns in table 'role':
 * @property string $user_id
 * @property string $organization_id
 * @property string $department_id
 * @property string $type
 * @property string $position
 * @property string $status
 * @property string $join_time
 *
 * @property bool $isAdmin
 * @property bool $isSuperAdmin
 *
 * @property Organization $organization
 * @property User $user
 * @property Department $department
 */
class Role extends ActiveRecord
{

    const TYPE_MEMBER = 'member';
    const TYPE_ADMIN = 'admin';
    const TYPE_SUPER_ADMIN = 'super';

    const STATUS_INVITED = 'invited';
    const STATUS_JOINT = 'joint';
    const STATUS_ARCHIVED = 'archived';

    /** @var  string only for invite */
    public $email;

    /** @var  boolean only for invite */
    public $is_admin = null;


    /**
     * @param bool $strict only admin, false if super admin
     * @return bool
     */
    public function getIsAdmin($strict = false) {
        return (!$strict && $this->type != self::TYPE_MEMBER) || $this->type == self::TYPE_ADMIN;
    }

    /**
     * @return bool true if super admin
     */
    public function getIsSuperAdmin() {
        return $this->type == self::TYPE_SUPER_ADMIN;
    }

    /**
     * @param $organization_id
     * @param $user_id
     * @param array $condition
     * @return Role
     */
    public function findFor($organization_id, $user_id, $condition = []) {
        return $this->findByPk(['organization_id' => $organization_id, 'user_id' => $user_id], $condition);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'role';
    }


    /**
     * @return $this
     */
    public function superAdminOnly() {
        $this->getDbCriteria()->compare('type', self::TYPE_SUPER_ADMIN);
        return $this;
    }

    public function exceptMe($user_id = null) {
        if (! $user_id) {
            $user_id = O::app()->user->id;
        }
        $this->getDbCriteria()->compare('user_id', '<>' . $user_id);
        return $this;
    }


    public function getMostUsedPositions() {
        $criteria = new CDbCriteria();
        $criteria->select = 'position';
        $criteria->group = 'position';
        $criteria->compare('organization_id', $this->organization_id);
        if ($this->department_id)
            $criteria->addCondition('department_id IS NOT NULL');
        else
            $criteria->addCondition('department_id IS NULL');
        $criteria->order = 'COUNT(*) DESC';

        $options = O::app()->db->createCommand()
            ->select('position')
            ->from($this->tableName())
            ->where('organization_id = :oid')
            ->andWhere('department_id IS ' . ($this->department_id ? 'NOT' : '') . ' NULL' )
            ->group('position')
            ->order('MAX(join_time) DESC')
            ->queryColumn([':oid' => $this->organization_id]) ?: [];

        if ($this->department_id == null) {
            $template = [
                O::t('organizzy', 'Chairman'),
                O::t('organizzy', 'Vice Chairman'),
                O::t('organizzy', 'Secretary'),
                O::t('organizzy', 'Treasurer'),
                O::t('organizzy', 'Vice Secretary'),
                O::t('organizzy', 'Vice Treasurer'),
            ];
        } else {
            $template = [
                O::t('organizzy', 'Head'),
                O::t('organizzy', 'Staff'),
            ];
        }

        foreach($template as $v) {
            if (!in_array($v, $options))
                $options[] = $v;
        }
        return $options;
    }

    public function beforeSave() {
        if ($this->department_id) {
            $this->type = $this->is_admin ? self::TYPE_ADMIN : self::TYPE_MEMBER;
        } else {
            $this->type = self::TYPE_SUPER_ADMIN;
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
            ['email', 'email', 'on' => 'invite'],
            ['email', 'checkEmail', 'on' => 'invite'],
            ['email, is_admin', 'safe'],

            array('user_id, organization_id', 'required'),
            array('position', 'length', 'max'=>32),
            array('department_id, type, status, join_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('user_id, organization_id, department_id, type, position, status, join_time', 'safe', 'on'=>'search'),
        );
    }


    /**
     * @param string $attr
     */
    public function checkEmail($attr) {
        /** @var User $user */
        $user = User::model()->findByAttributes(['email' => $this->email], ['select' => 'id']);
        if ($user) {
            /** @var Role $role */
            $role = self::model()->findByPk(
                ['user_id' => $user->id, 'organization_id' => $this->organization_id],
                ['select' => 'status']
            );
            if ($role) {
                if ($role->status == self::STATUS_INVITED) {
                    $this->addError($attr, Yii::t('organizzy', 'Email has been invited'));
                } else {
                    $this->addError($attr, Yii::t('organizzy', 'Email has been joint this organization'));
                }
            } else {
                // SET USER ID
                $this->user_id = $user->id;
            }
        } else {
            $this->addError($attr, Yii::t('organizzy', 'Email not found'));
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
            'user' => [self::BELONGS_TO, 'User', 'user_id'],
            'organization' => [self::BELONGS_TO, 'Organization', 'organization_id'],
            'department' => [self::BELONGS_TO, 'Department', 'department_id'],
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            //'user_id' => 'User',
            //'organization_id' => 'Organization',
            //'department_id' => 'Department',
            //'type' => 'Type',
            'position' => O::t('organizzy', 'Role'),
            'is_admin' => O::t('organizzy', 'Is Admin'),
            //'status' => 'Status',
            //'join_time' => 'Join Time',
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

        $criteria->compare('user_id',$this->user_id,true);
        $criteria->compare('organization_id',$this->organization_id,true);
        $criteria->compare('department_id',$this->department_id,true);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('position',$this->position,true);
        $criteria->compare('status',$this->status,true);
        $criteria->compare('join_time',$this->join_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Role the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}