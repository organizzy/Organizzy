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


//
if (isset($_ENV['DATABASE_URL']) && preg_match('#postgres://(\w+):(\w+)@([\w-.]+):(\d+)/(\w+)#', $_ENV['DATABASE_URL'], $m)) {
    define('DB_DRIVER', 'pgsql');
    define('DB_USER', $m[1]);
    define('DB_PASSWORD', $m[2]);
    define('DB_HOST', $m[3]);
    define('DB_PORT', $m[4]);
    define('DB_NAME', $m[5]);
}
elseif (file_exists($configFile  = __DIR__ . '/../config.php')) {
    include $configFile;
} else {
    die ('config.php not found');
}

defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

if (YII_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

require_once(__DIR__ . '/../vendor/yiisoft/yii/framework/yii.php');
require_once(__DIR__ . '/../protected/organizzy.php');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, POST');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-type, *');
    header('Access-Control-Max-Age: 1728000');

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header('HTTP/1.1 204 No Content');
        return;
    }
}
O::createOrganizzyApplication(__DIR__.'/../protected/config/main.php')->run();
