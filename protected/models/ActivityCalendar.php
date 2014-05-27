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
 * Class ActivityCalendar
 *
 * @property int $month
 * @property int $year
 * @property int $firstWeekDay
 * @property int $numDays
 */
class ActivityCalendar extends CModel {

    /** @var  int */
    private $user_id;

    /** @var  Activity[][] */
    private $activities;

    /** @var  int[] */
    private $numActivities;

    /** @var  int */
    private $month;

    /** @var  int */
    private $year;

    /**
     * @param int $month
     * @param int $year
     * @param int $user_id
     */
    function __construct($month, $year, $user_id = null)
    {
        $this->user_id = $user_id ?: O::app()->user->id;
        $this->month   = $month;
        $this->year    = $year;
    }


    /**
     * @return Activity[][]
     */
    public function getAllActivities() {
        if ($this->activities == null) {
            /** @var Activity[] $models */
            $models = Activity::model()->onlyMine($this->user_id)->onlyMonth($this->month, $this->year)->findAll();
            $activities = [];
            foreach($models as $activity) {
                $d = intval(substr($activity->datetime, 8, 2));
                if (!isset($activities[$d]))
                    $activities[$d] = [];

                $activities[$d][] =& $activity;
            }
            $this->activities = $activities;
        }
        return $this->activities;
    }

    public function getNumActivitiesPerDay() {
        if ($this->numActivities == null) {
            $numActivities = [];
            $reader = O::app()->db->createCommand()
                ->select([
                        'EXTRACT(DAY FROM datetime) as day',
                        'COUNT(*) as cnt',
                        'COUNT( NULLIF(substr(type, 1, 6), \'event-\') ) as task_cnt',
                        'COUNT( NULLIF(substr(type, 1, 5), \'task-\') ) as event_cnt'
                    ])
                ->from(Activity::model()->tableName())
                ->where('user_id = :uid')
                ->andWhere('datetime >= :first_day')
                ->andWhere('datetime < :last_day')
                ->group('day')
                ->query([
                        ':uid' => $this->user_id,
                        ':first_day' => sprintf('%04d-%02d-01', $this->year, $this->month),
                        ':last_day' => sprintf('%04d-%02d-01', $this->year+($this->month==12?1:0),
                            ($this->month==12?1:$this->month+1)),
                    ]);

            foreach($reader as $row) {
                $numActivities[$row['day']] = [
                    'total' => intval($row['cnt']),
                    'event' => intval($row['event_cnt']),
                    'task' => intval($row['task_cnt']),
                ];
            }
            $this->numActivities = $numActivities;
        }
        return $this->numActivities;
    }

    /**
     * @param int $day
     * @return Activity[]
     */
    public function getActivitiesAt($day) {
        $activities = $this->getAllActivities();
        return  (isset($activities[$day])) ? $activities[$day] : [];
    }

    public function getFirstWeekDay() {
        return date('w', mktime(0, 0, 0, $this->month, 1, $this->year));
    }

    public function getNumDays() {
        return date('d', mktime(0, 0, 0, $this->month + 1, 0, $this->year));
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }



    /**
     * Returns the list of attribute names of the model.
     *
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        // TODO: Implement attributeNames() method.
    }
}