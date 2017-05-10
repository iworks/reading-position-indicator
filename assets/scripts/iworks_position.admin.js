var iworks_progress = (function($, w) {
    function init() {
        $('.wpColorPicker').wpColorPicker();
        $('.form-table').on('click', '[name=irpi_style]', iworks_progress.check);

        if ( $.fn.select2 ) {
            $('.iworks-options .select2').select2();
        }
        if ( $.fn.slider ) {
            $('.iworks-options .slider').each( function() {
                $(this).parent().append('<div class="ui-slider"></div>' );
                var target = $(this);
                var value = target.val();
                var max = $(this).data('max') || 100;
                var min = $(this).data('min') || 0;
                $('.ui-slider', $(this).parent()).slider({
                    value: value,
                    min: min,
                    max: max,
                    slide: function( event, ui ) {
                        target.val( ui.value );
                    }
                });
            });
        }
    }
    return {
        init: init,
    };
})(jQuery);

jQuery(document).ready(iworks_progress.init);

