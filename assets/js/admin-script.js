// Admin Scripts for Sendit Integration
jQuery(document).ready(function($) {
    console.log('WooCommerce Sendit Integration settings loaded.');

    // Password toggle functionality
    $('.toggle-password').click(function() {
        const input = $(this).closest('.password-container').find('input');
        
        // Toggle password visibility
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            $(this).removeClass('dashicons-visibility').addClass('dashicons-hidden');
        } else {
            input.attr('type', 'password');
            $(this).removeClass('dashicons-hidden').addClass('dashicons-visibility');
        }
    });

    // Handle enable/disable toggle
    $('input[name="wc_sendit_enabled"]').on('change', function() {
        if (this.checked) {
            $('.api-fields').slideDown();
        } else {
            $('.api-fields').slideUp();
        }
    });
});
