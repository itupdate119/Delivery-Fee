define(
    [
        'Magento_Ui/js/form/form',
        'jquery',
        'ko',
        'B2b_EdiFee/js/model/cached-response',
        'B2b_EdiFee/js/lib/jquery.autocomplete',
        'mage/cookies',
        'mage/translate'
    ],
    function (Component, $, ko, cachedResponse) {
        'use strict';
        var ajaxSettings = {
                type: "POST",
                showLoader: true,
                dataType: "json"
            }
        return Component.extend({
            /**
             * @override
             */
            isDisplayed: function () {
                return true;
            },
            initialize: function () {
                this._super();
                this.postcode_fee = ko.observable();
            },

            updateData: function (el) {
                //this.postcode_fee(data.postcode_fee);
            },

            afterRender: function (el) {
                var that = this;
                $(el).devbridgeAutocomplete({
                    minChars: 3,
                    serviceUrl: '/edifee/postcode/suggest',
                    showNoSuggestionNotice: true,
                    noSuggestionNotice: $.mage.__('Invalid Postcode'),
                    onSelect: function (suggestion) {
                        that.postcode_fee(suggestion.value);
                        that.collectTotals(suggestion.value);
                    },
                    onHint: function (hint) {
                        $(el).next().val(hint);
                    },
                    onInvalidateSelection: function () {
                        that.updateData({});
                    },
                    autoSelectFirst: true,
                    tabDisabled: true,
                    params: {
                        'form_key': $.mage.cookies.get('form_key'),
                        'isAjax': true
                    },
                    ajaxSettings: {
                        'type': 'POST',
                        'showLoader': true,
                        'dataType': 'json'
                    }
                });
                $(el).data('autocomplete').cachedResponse = cachedResponse;
            },

            collectTotals: function (postcode) {
                ajaxSettings.data = {
                    'isAjax': true,
                    'postcode': postcode,
                    'form_key': $.mage.cookies.get('form_key')
                };
                ajaxSettings.url = '/edifee/postcode/load';
                $.ajax(ajaxSettings)
                    .done(function (data) {
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        $.mage.__('Can not load Edi Fee.');
                    })
                    .success(function (response) {

                    })
            }
        });
    }
);
