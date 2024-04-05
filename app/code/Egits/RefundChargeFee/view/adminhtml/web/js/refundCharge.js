require(["jquery", "mage/validation", "mage/url"], function (
    $,
    validation,
    urlBuilder
) {
    $(document).ready(function () {
        console.log("Document ready!");

        $("#refund_fee_enabled").on("change", function () {
            console.log("Checkbox state changed!");

            var isChecked = $(this).is(":checked");
            var value = isChecked ? $(this).val() : ""; // Set value only if checked

            if (isChecked && value === "1") {
                console.log("Checkbox is checked and value is 1.");

                var linkUrl = urlBuilder.build("refund_charge/calculate"); // Adjust URL generation
                console.log(linkUrl);
                // var linkUrl = "refund_charge/calculate";
                var formKey = $('[name="form_key"]').val();

                // Send AJAX request
                sendAjaxRequest(linkUrl, formKey, value);
            } else {
                console.log("Checkbox is not checked or value is not 1.");
            }
        });
    });

    function sendAjaxRequest(url, formKey, value) {
        $.ajax({
            showLoader: true,
            url: url,
            type: "POST",
            dataType: "json",
            headers: {
                form_key: formKey,
            },
            data: {
                form_key: formKey,
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
    }
});
