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
 * Class ActivityController
 * Controller for list activities (events and tasks) of current user
 */
class ActivityController extends Controller {

    /**
     * Show all activities of current user
     */
    public function actionIndex() {
        if (isset($_GET['mode'])) {
            $_SESSION['home_mode'] = $_GET['mode'];
        }

        $mode = isset($_SESSION['home_mode']) ? $_SESSION['home_mode'] : 'list';
        if ($mode == 'calendar')
            $this->calendarMode();
        else
            $this->listMode();
    }

    /**
     * Show all activities of current user  as list
     */
    private function listMode() {
        /*
        $criteria = new CDbCriteria(array(
            'select' => 'id, title, begin_time, end_time',
            'join' => 'JOIN event_invite ei ON ei.eid = t.id',
            'condition' => 'uid = :uid',
            'params' => array(':uid' => $this->userId),
            'order' => 'begin_time',
        ));
        $events = Event::model()->findAll($criteria);
        */

        $this->render('list',array('models' => Activity::model()->onlyMine($this->userId)->withDetails()->findAll()));
    }


    /**
     * Show calendar
     */
    private function calendarMode() {
        $params = $this->getActionParams();
        $year = isset($params['y']) ? $params['y'] : date('Y');
        $month = isset($params['m']) ? $params['m']  : date('m');

        $model = new ActivityCalendar($month, $year, $this->userId);
        $this->render('calendar',array('model' => $model));
    }

    /**
     * list all activities at $date
     *
     * @param $date
     */
    public function actionDay($date) {
        $models = Activity::model()->onlyMine($this->userId)->onlyForDate($date)->withDetails()->findAll();

        $this->render('day', array(
                'models' => $models,
                'date' => $date,
            ));
    }


    public function actionGet($date) {
        $result = [];
        /** @var Activity $model */
        foreach (Activity::model()->onlyMine($this->userId)->onlyForDate($date)->withDetails()->findAll() as $model) {
            $item = ['id' => $model->id, 'type' => $model->type, 'title' => $model->getTitle(), 'time' => $model->datetime];
            if ($model->itemType == Activity::TYPE_EVENT) {

            }
            $result[] = $item;
        }
        echo json_encode($result);
    }


} 