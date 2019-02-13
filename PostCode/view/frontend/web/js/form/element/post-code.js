/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function ($, _, registry, Abstract) {
    'use strict';

    var timeout;

    return Abstract.extend({
        defaults: {
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            var country = registry.get(this.parentName + '.' + 'country_id'),
                options = country.indexedOptions,
                option;

            if (!value) {
                return;
            }

            option = options[value];

            if (!option) {
                return;
            }

            if (option['is_zipcode_optional']) {
                this.error(false);
                this.validation = _.omit(this.validation, 'required-entry');
            } else {
                this.validation['required-entry'] = true;
            }

            this.required(!option['is_zipcode_optional']);
        },

        /**
         * Callback that fires when 'value' property is updated.
         */
        onUpdate: function () {
            var value = this.value(),
                self = this;

            if (timeout != 'undefined' || timeout != null) {
                clearTimeout(timeout);
            }

            timeout = setTimeout(function () {
                $('body').trigger('processStart');
                $.ajax({
                    url: document.location.origin + '/gomage_postcode/address/get',
                    data: {postcode: value},
                    success: function (resp) {
                        if (!resp.error) {
                            var city = registry.get(self.parentName + '.' + 'city');
                            if (city) {
                                city.value(resp.data.city);
                            }
                            var country = registry.get(self.parentName + '.' + 'country_id');
                            if (country) {
                                country.value(resp.data.country);
                            }
                            var region = registry.get(self.parentName + '.' + 'region_id');
                            if (region) {
                                region.value(resp.data.region);
                                $('[name="region"]').each(function () {
                                    $(this).val(resp.data.region);
                                    $(this).change();
                                });
                            }

                            self.processResult(resp);
                        }
                        $('body').trigger('processStop');
                        self.bubble('update', self.hasChanged());
                        self.validate();
                    }
                });
            }, 650);
        },

        processResult: function(resp) {
            $.each(this.processResultFunctions, function(key, funcObject){
                require([funcObject.component], function(func){
                    func(resp, funcObject.config);
                });
            });
        },

        processResultFunctions: []
    });
});