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
 * Class AjaxHandler
 *
 *
 */
class AjaxHandler {
    /**
     * @param ActiveRecord $model
     */
    public static function handleFormError($model) {
        if ($model->hasErrors()) {
            self::returnScript('O.handleFormError("' . get_class($model) . '", ' . json_encode($model->getErrors()) . ');');
        }
    }

    /**
     * @param ActiveRecord $model
     */
    public static function afterDelete($model) {
        self::returnScript('$("#' . get_class($model) . '_' . $model->getPrimaryKey(true) . '").remove()');
    }

    /**
     * @param string $script
     */
    public static function returnScript($script) {
        //header('X-Ajax-Handler: eval');
        header('Content-type: application/javascript');
        echo $script;
        O::app()->end();
    }
} 