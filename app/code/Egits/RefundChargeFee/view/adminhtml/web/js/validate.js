define([
    'jquery',
    'mage/translate',
    'jquery/ui'
], function($){
    $.validator.addMethod(
        'validate-alphanum-with-symbols',
        function (value) {
            return /^[a-zA-Z0-9\s%$]+$/.test(value);
        },
        $.mage.__('Please enter only letters, numbers, or symbols like % and $.')
    );
});
