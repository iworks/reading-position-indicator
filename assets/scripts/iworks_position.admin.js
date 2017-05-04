var iworks_progress = (function($, w) {
    function init() {
        $('.wpColorPicker').wpColorPicker();
        iworks_progress.check();
        $('.form-table').on('click', '[name=irpi_style]', iworks_progress.check);
    }

    function check() {
        val = $('[name=irpi_style]:checked').val();
        if ( 'solid' == val) {
            $('#tr_color2, #tr_color3').hide();
        } else if ( 'transparent' == val) {
            $('#tr_color2, #tr_color3').hide();
        } else if ( 'gradient' == val) {
            $('#tr_color2').show();
        }
    }

    return {
        init: init,
        check: check,
    };
})(jQuery);

jQuery(document).ready(iworks_progress.init);

