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
    var $body = $(document.body);

    main.init = function(baseUrl) {
        navigation.setBaseUrl(baseUrl);

        var currentPage = _organizzy.getStartUpPage() || navigation.getCurrentPage();
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
    // TODO: use registerBackHandler()
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

    if (window.cordova) {
        $body.on('pagechange', function(){
            $('input[type="date"]')
                .attr({'type': 'text', 'readonly': 1})
                .click(function () {
                    var $this = $(this);
                    var date;
                    if ($this.val() != '') {
                        date = new Date($this.val());
                    } else {
                        date = new Date();
                    }

                    window.datePicker.show({date: date, mode: 'date'}, function(date) {
                        if (! isNaN(date.getTime())) {
                            $this.val(date.getUTCFullYear() + '-' + (1+date.getMonth()) + '-' + date.getDate());
                        }
                    });
                });

            $('input[type="time"]')
                .attr({'type': 'text', 'readonly': 1})
                .click(function () {
                    var $this = $(this);
                    var date = new Date();
                    if ($this.val() != '') {
                        var t = $this.val().split(':');
                        date.setHours(t[0], t[1], t[2]);
                    }

                    window.datePicker.show({date: date, mode: 'time'}, function(date) {
                        if (! isNaN(date.getTime())) {
                            $this.val(date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds());
                        }
                    });
                });
        });
    }


    //---------------------
    // FLASH
    //---------------------
    $(document).on('click', '#flash-container', function() {
        $(this).hide();
    });

    var flashTimeout = null;
    $body.on('pagechange', function(){
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

    $body.on('pagechange', function(e){
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
    $(document).on('click', '#profile-photo', function(){
        var $elm =$('#photo-full');
        $elm.css('backgroundImage', 'url(' + $elm.attr('data-photo') + ')').fadeIn();

    });

    if (window.cordova) {
        $(document).on('click', '.photo-upload', function(e){
            var targetUrl = $(e.currentTarget).attr('data-target');
            var source = navigator.camera.PictureSourceType.PHOTOLIBRARY;
            if ($(e.currentTarget).attr('data-source') == 'camera') {
                source = navigator.camera.PictureSourceType.CAMERA;
            }

            function uploadPhoto(imageURI) {
                var options = new FileUploadOptions();
                options.fileKey = "file";
                options.fileName = imageURI.substr(imageURI.lastIndexOf('/')+1)+'.png';
                options.mimeType = "text/plain";

                options.params = {};

                var ft = new FileTransfer();
                $('#loader').fadeIn();
                ft.upload(imageURI, encodeURI(targetUrl), win, fail, options);
            }

            function win(e) {
                var response = JSON.parse(e.response);
                if (response.status == 'OK') {
                    $('#profile-photo').css('backgroundImage', 'url(' + response.result.thumb + ')');
                    $('#photo-full').css('backgroundImage', 'url(' + response.result.normal + ')');
                } else {
                    alert("Upload error: " + response.error);
                }
                $('#loader').fadeOut();
                console.log(e);
            }

            function fail(e) {
                console.error(e);
                $('#loader').fadeOut();
                alert("Upload error");
            }

            navigator.camera.getPicture(uploadPhoto,
                function(message) { console.log(message) },
                {
                    destinationType: navigator.camera.DestinationType.FILE_URI,
                    sourceType: source,
                    encodingType: navigator.camera.EncodingType.JPEG,
                    quality: 80,
                    targetWidth: 800,
                    targetHeight: 800
                }
            );
        });

    }



    return main;
});
