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
 * This is the model class for table "task_progress".
 *
 * The followings are the available columns in table 'task_progress':
 * @property string $id
 * @property string $task_id
 * @property string $reporter_id
 * @property string $report_time
 * @property string $progress
 * @property string $comment
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $reporter
 */
class TaskProgress extends ActiveRecord
{

    protected function afterSave() {
        parent::afterSave();
        if ($this->progress >= 100) {
            Task::model()->updateByPk($this->task_id, ['done' => true]);
        }
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_progress';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, reporter_id', 'required'),
			array('report_time, progress, comment', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, task_id, reporter_id, report_time, progress, comment', 'safe', 'on'=>'search'),
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
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
			'reporter' => array(self::BELONGS_TO, 'User', 'reporter_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'progress' => _t('Progress (%)'),
			'comment' => _t('Comment'),
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TaskProgress the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
