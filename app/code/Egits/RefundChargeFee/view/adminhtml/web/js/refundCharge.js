require(["jquery", "mage/validation"], function ($) {
    $(document).ready(function () {
        console.log("Document ready!");

        $("#refund_fee_enabled").on("change", function () {
            console.log("Checkbox state changed!");

            var formKey = $('[name="form_key"]').val();
            console.log(formKey);

            var orderId = $(this).data("order-id");
            console.log(orderId);

            var value = $(this).val();
            console.log("Checkbox value:", value);

            if ($(this).is(":checked") && value === "1") {
                console.log("Checkbox is checked and value is 1.");

                $.ajax({
                    url: "/index.php/admin/refund_fee/refund/refundCalculate", // Corrected URL
                    type: "POST",
                    dataType: "json",
                    data: {
                        orderId: orderId,
                        value: value,
                        form_key: window.FORM_KEY,
                    },
                    success: function (response) {
                        console.log("AJAX request successful!");
                        // Handle success response
                        console.log(response);
                        console.log(response.totalRefunded);

                        $("#refund_fee_amount").text(response.totalRefunded);
                        $("#refund_fee_amount").show(); // Show the refund fee amount span

                        // Store the value of the checkbox in a hidden input field
                        $("#refund_fee_value_input").val(value);
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX request failed:", error);
                        // Handle error
                        console.error(error);
                    },
                });
            } else {
                console.log("Checkbox is not checked or value is not 1.");
                $("#refund_fee_amount").hide(); // Hide the refund fee amount span
            }
        });
    });
});
