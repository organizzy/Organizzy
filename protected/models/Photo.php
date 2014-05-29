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
 * This is the model class for table "photo".
 *
 * The followings are the available columns in table 'photo':
 * @property string $id
 * @property string $file_name
 * @property string $url
 * @property integer $width
 * @property integer $height
 * @property string $upload_time
 *
 * The followings are the available model relations:
 * @property User[] $users
 * @property Organization[] $organizations
 *
 * @method Photo findByPk(int $id)
 */
class Photo extends ActiveRecord
{

    /** @var  CUploadedFile */
    public $file;

    /** @var  string */
    private $old_file_name;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'photo';
    }

    /**
     * @return string url to the photo
     */
    public function __toString() {
        return $this->url;
    }

    /**
     * @param null|string $size
     * @return string
     */
    public function getUrl($size = null) {
        if ($size) {
            return O::app()->getBaseUrl(true) . $this->url . '-' . $size . '.jpg';
        } else
            return $this->url;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('width, height', 'numerical', 'integerOnly'=>true),
            array('file_name, url', 'length', 'max'=>256),
            array('upload_time', 'safe'),

            // The following rule is used by search().
            //array('id, file_name, url, width, height, upload_time', 'safe', 'on'=>'search'),

            ['file', 'file', 'types'=>'jpg, jpeg, png, gif', 'maxSize' => 2097152],

        );
    }

    public function afterFind() {
        $this->old_file_name = $this->file_name;
        parent::afterFind();
    }

    public function beforeValidate() {
        if (! ($this->file instanceof CUploadedFile)) {
            $this->file = CUploadedFile::getInstance($this, 'file');
        }

        return parent::beforeValidate();
    }

    public function beforeSave() {
        $uploadDir = O::app()->basePath . '/../web/photos/';
        $fileName = $this->file->name;
        do {
            $fileName = md5($fileName . time()) . '.' . $this->file->getExtensionName();
            $filePath = $uploadDir . $fileName;
        } while (file_exists($filePath));

        $this->file->saveAs($filePath);
        $this->file_name = $filePath;

        $this->url = O::app()->baseUrl . '/photos/' . $fileName;

        $ir = new ImageResizer($filePath);
        $ir->saveAs($filePath . '-s.jpg', 42, 42, 50);
        $ir->saveAs($filePath . '-m.jpg', 100, 100, 70);

        @unlink($this->old_file_name);
        @unlink($this->old_file_name . '-s.jpg');
        @unlink($this->old_file_name . '-m.jpg');
        $this->old_file_name = $filePath;

        return parent::beforeSave();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'users' => array(self::HAS_MANY, 'User', 'photo_id'),
            'organizations' => array(self::HAS_MANY, 'Organization', 'logo_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'file_name' => 'File Name',
            'url' => 'Url',
            'width' => 'Width',
            'height' => 'Height',
            'upload_time' => 'Upload Time',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Photo the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}