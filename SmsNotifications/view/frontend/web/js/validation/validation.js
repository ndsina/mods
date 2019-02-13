(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define([
            "jquery",
            "mage/validation",
            "mage/translate"
        ], factory);
    } else {
        factory(jQuery);
    }
}(function ($) {
    "use strict";

    $.each({
        'validate-sms-phone-number': [
            function (value, element) {
                return this.optional(element) || /^\+?(?:[0-9][ |-]?){6,14}[0-9]$/.test(value);
            },
            $.mage.__('Please enter phone in international format, e. g. +1 234-567-89-89.')
        ]
    }, function (i, rule) {
        rule.unshift(i);
        $.validator.addMethod.apply($.validator, rule);
    });
}));