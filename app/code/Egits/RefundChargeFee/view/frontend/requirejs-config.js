var config = {
    map: {
        "*": {
            validation: "mage/validation/validation",
        },
    },
    shim: {
        label: {
            deps: ["jquery"],
        },
    },
    paths: {
        "Magento_Ui/js/lib/validation/validator": "mage/validation/validation", // Define path for mage/validation/validation
        "mage/url": "Magento_Ui/js/core/url", // Define path for mage/url
    },
};
