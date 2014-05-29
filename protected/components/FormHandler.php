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

class FormHandler {

    /**
     * Save $model with attribute fetched from $_POST
     *
     * @param ISavableModel $model
     * @return bool true if saving success
     */
    public static function save($model) {
        $modelName = CHtml::modelName($model);
        if (isset($_POST[$modelName])) {
            $model->setAttributes($_POST[$modelName]);
            if ($model->validate() && $model->save()) {
                return true;
            }
            elseif (O::app()->getIsAjaxRequest()) {
                AjaxHandler::handleFormError($model);
            }
        }
        return false;
    }

    /**
     * save the $model, and redirect to $url when success
     *
     * @param ISavableModel $model model to be saved
     * @param string|array $url
     */
    public static function saveRedirect($model, $url) {
        if (self::save($model)) {
            O::app()->controller->redirect($url);
        }
    }

    /**
     * save the model, and redirect to action view when success
     *
     * @param ISavableModel $model
     * @param string $idAttr name of attribute which is used for saving id of the model
     */
    public static function saveRedirectView($model, $idAttr = 'id') {
        $id = $model->$idAttr;
        self::saveRedirect($model, ['view', 'id' => $id]);
    }
} 