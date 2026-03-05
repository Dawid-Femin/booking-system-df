/* Public JavaScript */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Slot selection
        $('.booking-slot-button').on('click', function() {
            $('.booking-slot-button').removeClass('selected');
            $(this).addClass('selected');
            
            var start = $(this).data('start');
            var end = $(this).data('end');
            
            $('#start_datetime').val(start);
            $('#end_datetime').val(end);
        });
        
        // Form validation
        $('#booking-form').on('submit', function(e) {
            if (!$('#start_datetime').val()) {
                e.preventDefault();
                alert(bookingSystemPublic.strings.select_slot);
                return false;
            }
            
            var email = $('#patient_email').val();
            if (!validateEmail(email)) {
                e.preventDefault();
                alert(bookingSystemPublic.strings.invalid_email);
                return false;
            }
            
            var phone = $('#patient_phone').val();
            if (!validatePhone(phone)) {
                e.preventDefault();
                alert(bookingSystemPublic.strings.invalid_phone);
                return false;
            }
        });
        
        function validateEmail(email) {
            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        function validatePhone(phone) {
            var re = /^[0-9+\s-]{9,15}$/;
            return re.test(phone);
        }
    });
    
})(jQuery);
