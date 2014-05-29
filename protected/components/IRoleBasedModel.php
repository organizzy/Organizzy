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

interface IRoleBasedModel {

    /**
     * Array of access rules.
     * Access rule format:
     * [
     *     'action' => string or string[] of <b>view</b>, <b>create</b>, <b>update</b>, <b>delete</b>, etc. or * for all actions
     *
     *     'allow' => [optional] true if action is allowed
     *
     *     // role based [optional] (only if 'allow' not specified)
     *     'organization' => organization id
     *     'department' => department id (for department's item)
     *     'role' => user role
     *
     * ]
     * @return array access rules of the model
     * @see AccessRule::can
     */
    public function accessRules();
} 