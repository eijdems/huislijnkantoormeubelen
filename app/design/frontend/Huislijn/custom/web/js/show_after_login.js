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
        // Toggle 'active' class on both click and hover
        $('.over_menu').on('mouseenter', function () {
            console.log('mouseenter');
            $(this).addClass('hovered'); // Use a different class for hover behavior
        }).on('mouseleave', function () {
            $(this).removeClass('hovered');
        });

        // Handle click event to toggle the active class
        $('.cusom_click').on('click', function (e) {
            e.preventDefault(); // Stop link redirection
            let parentMenu = $(this).closest('.over_menu');
            
            // Toggle the active class
            if (parentMenu.hasClass('active')) {
                parentMenu.removeClass('active');
            } else {
                $('.over_menu').removeClass('active'); // Close other menus
                parentMenu.addClass('active');
            }
        });

        // Close the submenu when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.over_menu').length) {
                $('.over_menu').removeClass('active');
            }
        });
    });
});
