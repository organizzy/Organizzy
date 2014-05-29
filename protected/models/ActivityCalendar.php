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
     * get number of activities of each day
     *
     * @return int[]
     */
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
     * Get week day of 1th
     *
     * @return int
     */
    public function getFirstWeekDay() {
        return date('w', mktime(0, 0, 0, $this->month, 1, $this->year));
    }

    /**
     * Get number of day
     *
     * @return int
     */
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
        // not necessary
    }
}