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
 * Class OListView
 */
class OListView extends CWidget {

    /** @var  CModel[] */
    public $models;

    /** @var  mixed */
    public $data;

    /** @var string|callback  */
    public $titleAttr = 'name';

    /** @var string|callback */
    public $descriptionAttr = 'description';

    /** @var string|callback */
    public $photoAttr = null;

    /** @var string|callback */
    public $linkAttr = null;

    /** @var null|callback */
    public $dividerCb = null;

    /** @var bool */
    public $createTag = true;

    /** @var callable */
    public $checkBox = null;

    /** @var string */
    public $class = '';

    public function run() {
        if ($this->checkBox)
            $this->class .= ' table-view-check';
        //$this->render(O::app()->basePath . '/view/list-view');
        $this->render('application.views.list-view');
    }

    public function fetchAttr($model, $name) {
        if (is_callable($name)) {
            return call_user_func($name, $model, $this);
        } elseif (is_string($name)) {
            return $model->$name;
        } else {
            return null;
        }
    }

    public function getDividerText($model) {
        if (is_callable($this->dividerCb)) {
            return call_user_func($this->dividerCb, $model, $this);
        }
        return null;
    }
}
