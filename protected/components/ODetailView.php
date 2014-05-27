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
 * Class ODetailView
 *
 */
class ODetailView extends CWidget {

    public $id = null;

    public $class = 'detail-view';

    public $dlTag = 'dl';

    public $dlOptions = [];

    public $dtTag = 'dt';

    public $dtOptions = [];

    public $ddTag = 'dd';

    public $ddOptions = [];


    public function init() {
        $options = $this->dlOptions ?: [];
        if ($this->id) $options['id'] = $this->id;
        $options['class'] = $this->class;
        echo CHtml::openTag($this->dlTag, $options);
    }

    /**
     * @param string $name
     * @param string $value
     * @param null|string $icon
     * @param array $dtOptions
     * @param array $ddOptions
     */
    public function show($name, $value, $icon = null, $dtOptions = [], $ddOptions = []) {
        if ($value) {
            if ($icon) $iconHtml = '<i class="icon fa ' . $icon . '"></i> ';
            else $iconHtml = '';
            echo CHtml::tag($this->dtTag, $this->mergeOptions($this->dtOptions, $dtOptions), $iconHtml . $name);
            echo CHtml::tag($this->ddTag, $this->mergeOptions($this->ddOptions, $ddOptions), $value);
        }
    }

    private function  mergeOptions($a, $b) {
        $c = [];
        if (isset($a['class'], $b['class']) && $a['class'] && $b['class']) {
            $c['class'] = $a['class'] . ' ' . $b['class'];
        }
        return array_merge($a, $b, $c);
    }

    public function run() {
        echo CHtml::closeTag($this->dlTag);
    }

} 