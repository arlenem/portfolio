(function($){
    if ($('.front-page').length) {
        var $animation_elements = $('.header-group h3, .header-group .answer');
        var $window = $(window);

        $window.on('scroll resize', check_if_in_view);
        $window.trigger('scroll');

        function check_if_in_view() {
            var window_height = $window.height();
            var window_top_position = $window.scrollTop();
            var window_bottom_position = (window_top_position + window_height);

            $.each($animation_elements, function() {
                var $element = $(this);
                var element_height = $element.outerHeight();
                var element_top_position = $element.offset().top;
                var element_bottom_position = (element_top_position + element_height);

                //check to see if this current container is within viewport
                if ((element_bottom_position + 50 >= window_top_position) && (element_top_position <= window_bottom_position)) {
                    $element.addClass('in-view');
                } else {
                    $element.removeClass('in-view');
                }
            });
            var $last_element = $('section');
            var l_element_top_position = $last_element.offset().top;
            if ($last_element.offset().top <= window_bottom_position ) {
                $('.header-group .hello, .header-group .hello2').css('color', 'transparent');
            } else {
                $('.header-group .hello, .header-group .hello2').css('color', '#FFFFFF');
            }
        }
    }
    
})(jQuery);