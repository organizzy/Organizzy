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
 * This is the customized base active record class.
 * All model classes for this application should extend from this base class.
 *
 *
 */
abstract class ActiveRecord extends CActiveRecord {

    /** @var bool use transaction when saving */
    public $useTransaction = false;

    /**
     * Save the current record. Start transaction if {@link $useTransaction} is set.
     *
     * @param bool $runValidation
     * @param array $attributes
     * @return bool
     * @throws Exception
     */
    public function save($runValidation=true,$attributes=null) {
        if ($this->useTransaction) {
            $transaction = $this->getDbConnection()->beginTransaction();
            try {
                $ret = parent::save($runValidation, $attributes);
                $transaction->commit();
                return $ret;
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        }
        else
            return parent::save($runValidation, $attributes);
    }

    /**
     * @param string|array $pk record's primary key
     * @return string
     */
    private function  getCacheName($pk) {
        if (is_array($pk)) {
            $buff = [];
            foreach ($this->getMetaData()->tableSchema->primaryKey as $key) {
                $buff[] = $key . '=' . $pk[$key];
            }
            $pk = implode(',', $buff);
        }
        return 'Organizzy:' . get_class($this) . ':' . $pk;
    }

    /**
     * Finds a single active record with the specified primary key.
     *
     * @param mixed $pk primary key value(s).
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return ActiveRecord the record found. Null if none is found.
     */
    public function findByPk($pk,$condition='',$params=array()) {
        $cacheName = $this->getCacheName($pk);
        if (($attributes = O::app()->cache->get($cacheName)) == false) {
            $model = parent::findByPk($pk, $condition, $params);
            if ($model)
                O::app()->cache->set($cacheName, $model->getAttributes(), 30);
        } else {
            $model = $this->populateRecord($attributes);
        }
        return $model;
    }

    protected function afterSave() {
        parent::afterSave();
        if (! $this->isNewRecord)
            $this->invalidateCache($this->primaryKey);
    }


    public function updateByPk($pk,$attributes,$condition='',$params=array()) {
        if (parent::updateByPk($pk, $attributes, $condition, $params)) {
            $this->invalidateCache($pk);
        }
    }

    public function invalidateCache($pk = null) {
        if ($pk == null) $pk = $this->getPrimaryKey();
        O::app()->cache->delete($this->getCacheName($pk));
    }


    protected function jsonSafeAttributes() {
        return [];
    }

    public function toArray($attributes = []) {
        $returnAttributes = [];

        foreach($this->jsonSafeAttributes() as $k => $v) {
            if (is_numeric($k)) {
                $returnAttributes[$v] = $v;
            } else {
                $returnAttributes[$k] = $v;
            }
        }

        foreach($attributes as $k => $v) {
            if (is_numeric($k)) {
                $returnAttributes[$v] = $v;
            } elseif ($v == null) {
                unset($returnAttributes[$k]);
            } else{
                $returnAttributes[$k] = $v;
            }
        }


        $array = [];
        foreach($returnAttributes as $k => $v) {
            if (($value = self::getPropertyRecursive($this, $k)) !== null)
                $array[$v] = $value;
        }
        return $array;
    }


    private static function getPropertyRecursive($obj, $prop) {
        $prop = explode('.', $prop, 2);
        $obj = $obj->{$prop[0]}; //isset($obj->{$prop[0]}) ? $obj->{$prop[0]} : null;
        if ($obj && count($prop) > 1) {
            $obj = self::getPropertyRecursive($obj, $prop[1]);
        }
        return $obj;

    }


    /**
     * @param bool $forceString
     * @return mixed|string
     */
    public function getPrimaryKey($forceString = false) {
        $pk = parent::getPrimaryKey();
        if ($forceString && is_array($pk)) {
            $tmp = [];
            foreach($this->getMetaData()->tableSchema->primaryKey as $k) {
                $tmp[] = $k .'_' . $pk[$k];
            }
            return implode('-', $tmp);
        }
        return $pk;
    }
} 