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

define(['jquery', './navigation'], function($, navigation){
    var user = {};

    user.login = function(sid){
        if (_organizzy.login) _organizzy.login(sid);
        navigation.clearCache();
    };

    user.logout = function() {
        if (_organizzy.logout) _organizzy.logout();
        navigation.clearCache();
    };

    return user;

});