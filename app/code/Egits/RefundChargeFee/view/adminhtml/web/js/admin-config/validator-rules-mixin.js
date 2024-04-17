define(["jquery"], function ($) {
    "use strict";
    return function (target) {
        $.validator.addMethod(
            "validate-fee-amount",
            function (value) {
                var intValue = parseInt(value);
                return intValue >= 0 && intValue <= 100;
            },
            $.mage.__("Fee Amount must be between 0 and 100.")
        );
        return target;
    };
});

// define(["jquery", "jquery/validate", "mage/translate"], function ($) {
//     "use strict";

//     return function (validator) {
//         validator.addMethod(
//             "validate-fee-amount",
//             function (value) {
//                 var intValue = parseInt(value);
//                 return intValue >= 0 && intValue <= 100;
//             },
//             $.mage.__("Fee Amount must be between 0 and 100.")
//         );

//         return validator;
//     };
// });
