(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize color picker
        if ($.fn.wpColorPicker) {
            $('.color-picker').wpColorPicker();
        }
        
        // Tab switching
        function initTabs() {
            var hash = window.location.hash;
            if (hash) {
                var tab = hash.replace('#', '');
                $('.nav-tab-wrapper a[href="#' + tab + '"]').trigger('click');
            }
        }
        
        // Button type toggle
        function toggleButtonOptions() {
            var selected = $('input[name="coffee_widget_settings[button_type]"]:checked').val();
            $('.button-option').hide();
            $('.button-option-' + selected).show();
        }
        
        $('input[name="coffee_widget_settings[button_type]"]').on('change', toggleButtonOptions);
        toggleButtonOptions();
        
        // Tab click handlers
        $('.nav-tab-wrapper a').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href').replace('#', '');
            $('.nav-tab-wrapper a').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.settings-tab-content').hide();
            $('#tab-' + target).show();
            window.location.hash = target;
        });
        
        initTabs();
        
        // Form validation
        $('form').on('submit', function(e) {
            var isValid = true;
            var buttonType = $('input[name="coffee_widget_settings[button_type]"]:checked').val();
            if (buttonType === 'png') {
                var pngUrl = $('#button_png_url').val();
                if (pngUrl && !/^https?:\/\//i.test(pngUrl)) {
                    alert(coffee_widget_admin.invalid_url);
                    isValid = false;
                }
            }
            return isValid;
        });
        
        // Preview update for style editor
        if ($('#custom_css').length) {
            var previewStyle = $('#preview-style');
            $('#custom_css').on('keyup change', function() {
                previewStyle.html($(this).val());
            });
        }
    });
    
})(jQuery);
