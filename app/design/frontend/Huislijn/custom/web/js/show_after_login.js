require(['jquery', 'Magento_Customer/js/customer-data'], function ($, customerData) {
    $(document).ready(function () {
        // Get the customer data
        var customer = customerData.get('customer');

        // Check if customer is logged in
        if (customer().firstname) { 
            // Show elements with the "show_after_login" class
            $('.show_after_login').show();
        } else {
            // Hide elements if not logged in
            $('.show_after_login').hide();
        }

        // Update on customer data change
        customerData.get('customer').subscribe(function (customerData) {
            if (customerData.firstname) {
                $('.show_after_login').show();
            } else {
                $('.show_after_login').hide();
            }
        });
    });
});
