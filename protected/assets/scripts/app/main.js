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
"use strict";
define(['jquery', './navigation'], function($, navigation){
    var main = {};

    main.init = function(baseUrl) {
        navigation.setBaseUrl(baseUrl);

        var currentPage = navigation.getCurrentPage();
        if (currentPage) {
            navigation.changePage(currentPage);
        }
        else {
            navigation.changePage('/site/boot?v=' + 100);
        }
    };

    //---------------------------------------------
    // MENU HANDLER
    //---------------------------------------------
    var moreMenuShown = false;

    function toggleMenu(show) {
        if (show == undefined) {
            show = !moreMenuShown;
        }
        if (show)
            $('#more-menu').addClass('show');
        else
            $('#more-menu').removeClass('show');
        moreMenuShown = show;
    }

    $(document).on('click', '#more-menu', function(){
        toggleMenu(false);
    });

    document.addEventListener("menubutton", function(){
        //var $moreMenu = $('#more-menu');
        if (! navigation.isLoading()) {
            toggleMenu();
        }
        return false;
    }, false);

    $(document).on('click', '#tab-item-4', function(){
        //$('#more-menu').show();
        toggleMenu(true);
        //moreMenuShown = true;
        return false;
    });


    //---------------------------------------------
    // BACK BUTTON
    //---------------------------------------------
    document.addEventListener("backbutton", function(e){
        if (moreMenuShown) {
            toggleMenu(false);
        }
        else if (!navigation.back()) {
            //navigator.app.exitApp();
        }
        e.preventDefault();

    }, false);

    //---------------------
    // FORM
    //---------------------
    main.handleFormError = function(model, errors) {
        $('.errorMessage').hide();
        for(var attr in errors) {
            var name = model + '[' + attr + ']';
            var rows = $('[name="' + name + '"]').parent();
            if (rows.length > 0) rows.each(function(i, row){
                var $error = $(row).find('.errorMessage');
                if ($error.length == 0) {
                    $error = $('<div class="errorMessage"></div>');
                    $(row).append($error);
                }
                $error.html(errors[attr].join('<br />')).show();
            });
        }
    };

    //---------------------
    // FLASH
    //---------------------
    $(document).on('click', '#flash-container', function() {
        $(this).hide();
    });

    var flashTimeout = null;
    $(document.body).on('pagechange', function(){
        if (flashTimeout) clearTimeout(flashTimeout);

        flashTimeout = setTimeout(function(){
            $('#flash-container').hide();
        }, 3000);
    });


    //---------------------------------------------
    // TAB VIEW
    //---------------------------------------------
    function changeTab(id) {
        var $this = $('#' + id + '-selector');
        var $tabView = $this.parent().parent();
        $tabView.find('.tab-page.active').trigger('tabpagehide');
        $tabView.find('.selector a.active, .tab-page.active').removeClass('active');
        $this.addClass('active');
        $tabView.find('#' + id).addClass('active').trigger('tabpageshow');
    }

    $(document).on('click', '.tab-view .selector a', function(e){
        var $this = $(e.currentTarget);
        changeTab($this.attr('href').substr(1));

        history.replaceState({}, document.title, '#tab='+ $this.attr('href').substr(1));

        e.preventDefault();
    });

    $(document.body).on('pagechange', function(e){
        var hash = location.hash;
        if (hash && hash.indexOf('tab=')>0) {
            changeTab(hash.match(/tab=([^&]+)/)[1]);
        }
    });

    //---------------------------------------------
    // CHECK BOX LIST VIEW
    //---------------------------------------------
    $(document).on('click', '.table-view-check li', function(e){
        var $input = $(this).find('input[type=checkbox]');
        $input.prop('checked', !$input.prop('checked'));
    });


    //---------------------------------------------
    // PROFILE VIEW
    //---------------------------------------------
    $(document).on('click', '.profile-header .photo', function(){
       $('#Photo_file').click();
    });

    $(document).on('change', '#Photo_file', function() {
        $('#photo-upload-form').submit();
    });

    return main;
});
