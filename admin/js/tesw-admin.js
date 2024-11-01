(function ($) {
    'use strict';

    $(document).ready(function () {
        $('.tesw-toggle-input').on('change', function () {
            $('#tesw-plugin-container').toggleClass('tesw-dark-mode');
            teswUpdateTitleColor();
        });

        $('.tesw-click').on('click', function () {
            $('#tesw-plugin-container').toggleClass('tesw-dark-mode');
            teswUpdateTitleColor();
        });

        // Update the add_settings_field title color based on dark mode
        function teswUpdateTitleColor() {
            if ($('#tesw-plugin-container').hasClass('tesw-dark-mode')) {
                $('.wp-core-ui .form-table th').css('color', 'red');
                $('.wp-core-ui .form-table td').css('color', 'white');
                $('#tesw-plugin-container').css('background-color', 'black');
            } else {
                $('.wp-core-ui .form-table th').css('color', 'initial');
                $('.wp-core-ui .form-table td').css('color', 'initial');
                $('#tesw-plugin-container').css('background-color', 'white');
            }
        }

        // Check the user's preference and set the initial state accordingly
        if (localStorage.getItem('teswDarkModeEnabled') === 'true') {
            $('.tesw-toggle-input').prop('checked', true);
            $('#tesw-plugin-container').addClass('tesw-dark-mode');
        } else {
            $('.tesw-toggle-input').prop('checked', false);
            $('#tesw-plugin-container').removeClass('tesw-dark-mode');
        }

        // Store user's preference on toggle change
        $('.tesw-toggle-input').on('change', function () {
            if ($(this).is(':checked')) {
                localStorage.setItem('teswDarkModeEnabled', 'true');
            } else {
                localStorage.setItem('teswDarkModeEnabled', 'false');
            }
        });

        // Initial check for dark mode and update the title color accordingly
        teswUpdateTitleColor();
    });
    jQuery(document).ready(function ($) {
        // Get the checkbox elements and the color picker elements
        var teswButtonCheckbox = $('#tesw_button_color_enable');
        var teswButtonColorPicker = $('#tesw_button_color');
        var teswTextCheckbox = $('#tesw_text_color_enable');
        var teswTextColorPicker = $('#tesw_text_color_css');

        // Function to handle the visibility of color picker based on checkbox state
        function teswHandleColorPickerVisibility(tesw_checkbox, tesw_colorPicker) {
            if (tesw_checkbox.is(':checked')) {
                tesw_colorPicker.slideDown('slow');
            } else {
                tesw_colorPicker.slideUp('fast');
            }
        }

        // Check the initial state of the checkboxes and hide/show the color pickers accordingly
        teswHandleColorPickerVisibility(teswButtonCheckbox, teswButtonColorPicker);
        teswHandleColorPickerVisibility(teswTextCheckbox, teswTextColorPicker);

        // Toggle the visibility of the color pickers based on checkbox changes
        teswButtonCheckbox.on('change', function () {
            teswHandleColorPickerVisibility(teswButtonCheckbox, teswButtonColorPicker);
        });

        teswTextCheckbox.on('change', function () {
            teswHandleColorPickerVisibility(teswTextCheckbox, teswTextColorPicker);
        });
    });

})(jQuery);
