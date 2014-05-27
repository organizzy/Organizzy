/**
 * Created by abie on 5/3/14.
 */

var O = window.O || {};

(function($, undefined){
    var O = window.O;

    $(document).ready(function(){
        $('#loader').hide();
    });

    O.init = function(baseServer) {
        O.navigation.serverBase = baseServer;

        if (localStorage.getItem('sessionId')) {
            O.navigation.changePage('/activity/index');
        } else {
            O.navigation.changePage('/user/login');
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
        if (! O.navigation.isLoading()) {
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
        else if (!O.navigation.back()) {
            //navigator.app.exitApp();
        }
        e.preventDefault();

    }, false);

    //---------------------
    // FORM
    //---------------------
    O.handleFormError = function(model, errors) {
        $('.errorMessage').hide();
        for(var attr in errors) {
            var name = model + '[' + attr + ']';
            var row = $('[name="' + name + '"]').parent();
            if (row.length > 0) row.forEach(function(row){
                var $error = $(row).find('.errorMessage');
                if ($error.length == 0) {
                    $error = $('<div class="errorMessage"></div>');
                    $(row).append($error);
                }
                //var errorsLi = [];
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


})(jQuery);
