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

namespace lib\utils;

/**
 * Class Random
 *
 * @package lib\utils
 */
class Random {
    const CASE_LOWER = 1;
    const CASE_UPPER = 2;
    const CASE_BOTH = 3;

    private static $packs = [
        self::CASE_LOWER => '0123456789abcdefghijklmnopqrstuvwxyz',
        self::CASE_UPPER => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        self::CASE_BOTH =>  '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    ];

    private static $unsafe = [
        '0' => 1, 'O' => 1, 'l' => 1, '1' => 1, 'I' => 1
    ];

    /**
     * @param int $length string
     * @param int $case CASE_LOWER, CASE_UPPER, or CASE_BOTH
     * @param bool $safe only safe character
     * @return string
     */
    public static function generate($length, $case = self::CASE_BOTH, $safe = false) {
        $pack = self::$packs[$case];
        $r = [];
        $n = strlen($pack);

        $i = 0;
        while($i<$length) {
            $c = $pack[mt_rand(0, $n - 1)];
            if (!$safe || !isset(self::$unsafe[$c])) {
                $r[] = $c;
                $i++;
            }
        }

        return implode('', $r);
    }
} 