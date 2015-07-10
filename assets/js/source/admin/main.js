(function ($) {
    'use strict';

    $(function () {

        // Apply Chosen
        $('.technicaldocs-chosen').each(function () {
            $(this).chosen($(this).data());
        });
    });
})(jQuery);