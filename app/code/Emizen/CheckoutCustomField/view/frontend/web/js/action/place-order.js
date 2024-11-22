define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/place-order',
    'jquery'
], function (quote, urlBuilder, customer, placeOrderService, $) {
    'use strict';

    return function (paymentData, messageContainer) {
        console.log('Custom Place Order Script Loaded!');

        // Check if a radio button is selected
        const isChecked = $('input[name="customCheckbox"]:checked').length > 0;
        console.log('Radio checked status:', isChecked);

        if (!isChecked) {
            console.error('No option is selected. Stopping place order.');

            // Show error message dynamically
            const errorContainer = $('.custom_field_quote div[data-bind*="validation"]');
            errorContainer.text('Please select an option').css('color', 'red').show();

            // Stop execution
            return new Promise((resolve, reject) => {
                reject({
                    error: true,
                    message: 'No option selected. Please select one option.'
                });
            });
        }

        // Clear error message on success
        $('.custom_field_quote div[data-bind*="validation"]').hide();

        // Construct payload for place order request
        const payload = {
            cartId: quote.getQuoteId(),
            billingAddress: quote.billingAddress(),
            paymentMethod: paymentData
        };

        let serviceUrl;

        if (customer.isLoggedIn()) {
            serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
        } else {
            serviceUrl = urlBuilder.createUrl('/guest-carts/:quoteId/payment-information', {
                quoteId: quote.getQuoteId()
            });
            payload.email = quote.guestEmail;
        }

        // Place order
        return placeOrderService(serviceUrl, payload, messageContainer);
    };
});
