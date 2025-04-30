require(['jquery', 'Magento_Customer/js/customer-data'], function ($, customerData) {
    $(document).ready(function () {
        // Get the customer data
        var customer = customerData.get('customer');

        // Function to update the href dynamically
        function updateHref(customer) {
            if (customer && customer.firstname) {
                // Set the correct absolute URL for downloads
                $('.show_after_login a').attr('href', '/customdownload'); // Correct relative path
                $('.show_after_login').show(); // Ensure visibility
            } else {
                // Set the URL to the login page for unauthenticated users
                $('.show_after_login a').attr('href', '/customer/account/login');
                //$('.show_after_login').hide(); // Hide the element
            }
        }

        // Initial call to update the href
        updateHref(customer());

        // Subscribe to customer data changes
        customer.subscribe(function (updatedCustomer) {
            updateHref(updatedCustomer);
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
