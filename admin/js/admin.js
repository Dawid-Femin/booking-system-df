/* Admin JavaScript */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Confirmation dialogs
        $('.booking-confirm-action').on('click', function(e) {
            if (!confirm(bookingSystemAdmin.strings.confirm_confirm)) {
                e.preventDefault();
            }
        });
        
        $('.booking-cancel-action').on('click', function(e) {
            if (!confirm(bookingSystemAdmin.strings.confirm_cancel)) {
                e.preventDefault();
            }
        });
    });
    
})(jQuery);
