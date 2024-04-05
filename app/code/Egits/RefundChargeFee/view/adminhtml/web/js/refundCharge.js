require(["jquery", "mage/validation"], function ($) {
    $(document).ready(function () {
        console.log("Document ready!");

        $("#refund_fee_enabled").on("change", function () {
            console.log("Checkbox state changed!");

            var formKey = $('[name="form_key"]').val();
            console.log(formKey);

            var value = $(this).val();
            console.log("Checkbox value:", value);

            if ($(this).is(":checked") && value === "1") {
                console.log("Checkbox is checked and value is 1.");

                $.ajax({
                    url: "/admin/refund_fee/index/refundCalculate", // Corrected URL
                    type: "POST",
                    dataType: "json",
                    headers: {
                        formKey: formKey,
                    },
                    data: {
                        value: value,
                    },
                    success: function (response) {
                        console.log("AJAX request successful!");
                        // Handle success response
                        console.log(response);
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX request failed:", error);
                        // Handle error
                        console.error(error);
                    },
                });
            } else {
                console.log("Checkbox is not checked or value is not 1.");
            }
        });
    });
});
