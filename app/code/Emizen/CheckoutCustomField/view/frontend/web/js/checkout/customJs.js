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
                        referenceNumber: ko.observable(''),  // Observable for the reference number input
                        uploadedFiles: ko.observableArray([]) // Observable for multiple uploaded files
                    });

                var self = this;

                // Function to track changes in reference number input
                this.onReferenceNumberChange = function () {
                    var referenceVal = self.referenceNumber(); // Get the updated reference number
                    var checkVal = self.CheckVals(); // Get the current radio button selection
                    self.sendDataToBackend(checkVal, referenceVal); // Send data via AJAX
                };

                // Listen for changes in CheckVals observable
                this.CheckVals.subscribe(function (newValue) {
                    var referenceVal = self.referenceNumber(); // Get the latest reference number
                    self.sendDataToBackend(newValue, referenceVal); // Send data when CheckVals changes
                });

                // Function to handle file uploads
                this.onImageUpload = function (data, event) {
                    var files = event.target.files;
                    if (files && files.length > 0) {
                        console.log("Files selected:", files);
                        var fileArray = Array.from(files); // Convert FileList to an array
                        self.uploadedFiles(fileArray); // Update the observable array
                    }
                };

                return this;
            },

            // Function to handle sending data via AJAX
            sendDataToBackend: function (checkVal, referenceVal) {
                var linkUrls = url.build('module/checkout/saveInQuote');
                var formData = new FormData(); // FormData for file uploads
                var uploadedFiles = this.uploadedFiles(); // Access the observable array of files

                formData.append('checkVal', checkVal === 'quote' ? 1 : 0); // Add checkVal
                formData.append('reference_number', referenceVal); // Add reference number

                if (uploadedFiles.length > 0) {
                    uploadedFiles.forEach(function (file) {
                        formData.append('uploadedFiles[]', file, file.name); // Append files with unique keys
                    });
                }

                $.ajax({
                    showLoader: true,
                    url: linkUrls,
                    data: formData,
                    type: "POST",
                    processData: false, // Prevent jQuery from automatically processing data
                    contentType: false, // Allow FormData to handle content type
                    dataType: 'json'
                }).done(function (response) {
                    console.log('Request was successful', response);
                });
            }
        });
    }
);