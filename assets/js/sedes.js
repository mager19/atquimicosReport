(function ($) {

    // Auto-hide success message after 5 seconds
    $(document).ready(function () {
        var $notice = $('.atquimicos__notice');

        if ($notice.length > 0) {
            // Add click to dismiss functionality
            $notice.css('cursor', 'pointer').on('click', function () {
                $(this).addClass('fade-out');
                setTimeout(function () {
                    $notice.remove();
                }, 500);
            });

            // Auto-hide after 5 seconds
            setTimeout(function () {
                if ($notice.is(':visible')) {
                    $notice.addClass('fade-out');
                    setTimeout(function () {
                        $notice.remove();

                        // Clean up URL parameters after hiding message
                        if (window.history && window.history.replaceState) {
                            var url = new URL(window.location);
                            url.searchParams.delete('report');
                            url.searchParams.delete('sede');
                            window.history.replaceState({}, document.title, url.toString());
                        }
                    }, 500);
                }
            }, 5000);
        }
    });

})(jQuery);