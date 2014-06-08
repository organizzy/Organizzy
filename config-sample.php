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

return [
    'components' => [

        // Database connection
        'db' => [
            'connectionString' => 'pgsql:host=localhost;dbname=organizzy',
            'username' => 'root',
            'password' => 'password',
        ],

        'mail' => [
            'senderAddress' => 'admin@organizzy.org',
            'senderName' => 'Organizzy Administrator',
        ],

        // if you install apc on your php, uncomment these
//        'cache' => [
//            'class'=>'CApcCache',
//        ],
    ],

];