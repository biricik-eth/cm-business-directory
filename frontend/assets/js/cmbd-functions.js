
(function ($) {
    $(document).ready(function ($) {
        (function () {
            var po = document.createElement('script');
            po.type = 'text/javascript';
            po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(po, s);
        })();
        (function (d, s, id) {
            var fbAsyncInitCMA = function () {
                // Don't init the FB as it needs API_ID just parse the likebox
                FB.XFBML.parse();
            };
            if (typeof (window.fbAsyncInit) == 'function') {
                var fbAsyncInitOld = window.fbAsyncInit;
                window.fbAsyncInit = function () {
                    fbAsyncInitOld();
                    fbAsyncInitCMA();
                };
            } else {
                window.fbAsyncInit = fbAsyncInitCMA;
            }

            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id))
                return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    });
})(jQuery);