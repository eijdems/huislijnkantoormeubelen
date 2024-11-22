define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'mage/url'
    ],
    function (ko, $, Component, url) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Emizen_CheckoutCustomField/checkout/customCheckbox'
            },

            initObservable: function () {
                this._super()
                    .observe({
                        CheckVals: ko.observable(false),    // Observable for radio button selection
                        referenceNumber: ko.observable('')  // Observable for the reference number input
                    });

                var self = this;

                // The function that will be called on keyup to track the reference number
                this.onReferenceNumberChange = function () {
                    var referenceVal = $("#reference_number").val(); // Get the updated reference number value
                    console.log(referenceVal);
                    var checkVal = self.CheckVals(); // Get the selected value of the radio buttons
                    self.sendDataToBackend(checkVal, referenceVal); // Send both values via AJAX
                };

                // Listen for changes in CheckVals observable (radio buttons)
                this.CheckVals.subscribe(function (newValue) {
                    var referenceVal = $("#reference_number").val(); // Get the latest reference number
                    self.sendDataToBackend(newValue, referenceVal); // Send both values when CheckVals changes
                });

                return this;
            },

            // Function to handle sending data via AJAX
            sendDataToBackend: function (checkVal, referenceVal) {
                var linkUrls = url.build('module/checkout/saveInQuote');
                $.ajax({
                    showLoader: true,
                    url: linkUrls,
                    data: {
                        checkVal: checkVal === 'quote' ? 1 : 0, // Ensure it's 1 or 0 for "quote" or "order"
                        reference_number: referenceVal // Send the reference number
                    },
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    console.log('Request was successful');
                });
            }
        });
    }
);
