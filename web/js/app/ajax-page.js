(function($, undefined){
    var O = window.O;
    O.navigation = O.navigation || {};

    var $body = $(document.body);

    var loadPageXhr = undefined;
    var backUrl = undefined;

    O.navigation.serverBase = '';
    $.support.cors = true;

    function loadPage(url, data, options) {
        options = options || {};

        loadPageXhr = $.ajax({
            url: O.navigation.serverBase + url,
            context: document.body,
            data: data,
            type: data ? 'POST' : 'GET',
            processData: !(data instanceof FormData),
            contentType: (data instanceof FormData) ? false : 'application/x-www-form-urlencoded',
            timeout: 45000,

            xhrFields: {
                withCredentials: true
            },

            beforeSend: function(xhr) {
                //if (localStorage.getItem('sessionId'))
                //    xhr.setRequestHeader('Cookie', localStorage.getItem('sessionId'));
            },

            error: function( xhr, status, error ) {
                console.error({e: error, s: status, x: xhr});
                if (!xhr.cancel)
                    alert(error || status);
                $('#loader').hide();
                loadPageXhr = undefined;
            },

            success: function(data, status, xhr) {
                if (xhr.getResponseHeader('Content-type').indexOf('javascript') > 0) {
                    eval(data);
                }
                else {
                    replacePageContent(url, data, true, options);
                }

                $('#loader').hide();
                loadPageXhr = undefined;
            }
        });
    }

    function replacePageContent(url, content, saveCache, options) {
        //options = options || {};

        var iPos = content.indexOf('<!--page:');
        if (iPos >= 0) {
            iPos += 9;
            /** @namespace arg.backUrl */
            /** @namespace arg.title */
            var arg = JSON.parse(content.substr(iPos, content.indexOf('-->') - iPos));
            document.title = arg.title;

            var back = url.match(/return=([^&]+)/);
            if (back && back.length>1) {
                backUrl = decodeURIComponent(back[1]);
            }
            else {


                backUrl = arg.backUrl;
            }

            var hashUrl = url.match(/#.*/);
            hashUrl = hashUrl ? hashUrl[0] : '';

            if (saveCache && !arg.disableCache) {
                localStorage.setItem(cacheName(url), content);
            }

            //history.pushState(arg, document.title, arg.url + hashUrl);


            $body.attr('id', 'page-' + arg.id);
        }
        $body.html(content);
        $body.trigger('pagechange');
    }

    function changePage(url, options) {
        options = options || {};

        if (loadPageXhr) {
            loadPageXhr.cancel = true;
            loadPageXhr.abort();
        }

        if ($body.trigger('pagebeforechange') === false)
            return;

        $('#loader').attr('class', '').show();

        var loadFromServer = true;
        if (! options.disableCache && !options.data) {
            var cache = localStorage.getItem(cacheName(url));
            if (cache) {
                replacePageContent(url, cache, false);
                $('#flash-container').hide(); // todo: don't place here

                loadFromServer = options.cacheOnly != true;

                if (loadFromServer) {
                    $('#loader').attr('class', 'mini').show();
                }
            }
        }

        if (loadFromServer) {

            loadPage(url, options.data, options);
        }
    }

    //O.navigation.loadPage = loadPage;
    O.navigation.changePage = changePage;

    function cacheName(url) {
        return 'cache:' + url.match(/[^#]+/)[0];
    }


    O.navigation.clearCache = function(url) {
        if (url) {
            localStorage.removeItem(cacheName(url));
        } else {
            var len = localStorage.length;
            for(var i=0; i<len; i++) {
                var key = localStorage.key(i);
                if (key.substr(0, 6) == 'cache:') {
                    localStorage.removeItem(key);
                }
            }
        }

    };

    O.navigation.back = function() {
        if (backUrl) {
            changePage(backUrl);
            return true;
        }
        else if (navigator.app && navigator.app.exitApp) {
            navigator.app.exitApp();
        }
        return false;
    };

    O.navigation.isLoading = function() {
        return loadPageXhr != undefined;
    };

    O.navigation.cancel = function() {
        if (loadPageXhr) {
            loadPageXhr.cancel = true;
            loadPageXhr.abort();
            return truel
        }
        return false;
    };


    /*
     window.onpopstate = function(e) {
     if (e.state) {
     loadPage(e.state.url, null, true)
     e.preventDefault();
     } else {
     console.log(e);
     }
     };
     */

    $(document).on('click', 'a', function(e){
        var url = this.getAttribute('href');
        var $this = $(this);

        if (url && url[0] != '#') {
            if ($this.hasClass('btn-post')) {
                var ask = $this.attr('data-ask');
                e.preventDefault();

                if (!ask || confirm(ask)) {
                    changePage(this.href, {data: $this.attr('data-post')});
                }
            }

            else if ($this.hasClass('btn-back')) {
                if (!backUrl) backUrl = url;
                O.navigation.back();
            }

            else {

                changePage(url);
            }
            e.preventDefault();
        }
    });

    $body.on('pagechange', function(){
        $body.find('form').submit(onFormSubmit);
    });
    function onFormSubmit(e) {
        var $this = $(this);
        var url = $this.attr('action') || location.href;
        var method = ($this.attr('method') || 'get').toLowerCase();
        var data;
        if ($this.attr('enctype') == 'multipart/form-data')
            data = new FormData($this[0]);
        else
            data = $this.serialize();

        if (method == 'post') {
            changePage(url, {data: data});
        } else {
            changePage(url + (url.indexOf('?') >= 0 ? '&' : '?') + data);
        }
        e.preventDefault();
    }



    $(document).ready(function(){
        $(document.body).trigger('pagechange');
    });

//$('#loader').hide();
    //history.replaceState({url: location.href}, document.title, location.href);
    //$(document.body).trigger('pagechange');
})(jQuery);


