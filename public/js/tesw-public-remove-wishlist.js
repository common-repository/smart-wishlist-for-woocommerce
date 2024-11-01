(function ($) {
    'use strict';

    $(document).ready(function ($) {
        // Handle click event on remove product button
        $(document).on("click", ".tesw-remove-product", function () {
            var teswProductID = $(this).data("product-id");
            var $teswRow = $(this).closest("tr"); // Reference to the table row

            // Add loader class to the remove button
            $(this).addClass("loading");

            // Perform AJAX request to remove the product
            $.ajax({
                url: tesw_ajax_object.ajax_url,
                type: "POST",
                data: {
                    action: "tesw_remove_product_from_wishlist",
                    productID: teswProductID,
                    teswremovenonce: tesw_ajax_object.nonce
                },
                success: function (response) {
                    if (response.success) {
                        // Remove the table row from the wishlist table
                        $teswRow.remove();

                        // Check if there are any remaining products
                        if ($(".tesw-wishlist tr").length === 0) {
                            // Display the empty wishlist message
                            $(".tesw-wishlist").html("<p>" + tesw_ajax_object.empty_wishlist_message + "</p>");
                        }
                    } else {
                        // Display the error message
                        alert(tesw_ajax_object.remove_error);
                    }
                },
                complete: function () {
                    // Remove the loader class from the remove button
                    $(".tesw-remove-product").removeClass("loading");
                }
            });
        });
    });

    //multiple product remove from table

    $(document).on("click", "#tesw-apply-button", function () {
        var tesw_action = $(".tesw-action-select").val();

        if (tesw_action === "remove") {
            // Array to store the product IDs to be removed
            var tesw_productsToRemove = [];

            // Find all the checkboxes that are checked
            $(".tesw-product-checkbox:checked").each(function () {
                tesw_productsToRemove.push($(this).data("product-id"));
            });

            if (tesw_productsToRemove.length > 0) {
                // Add loader class to the "Apply Action" button
                $(this).addClass("loading");

                // Perform AJAX request to remove the products
                $.ajax({
                    url: tesw_ajax_object.ajax_url,
                    type: "POST",
                    data: {
                        action: "tesw_remove_multiple_products_from_wishlist",
                        productIDs: tesw_productsToRemove,
                        teswmultiremovenonce: tesw_ajax_object.nonce
                    },
                    success: function (response) {
                        if (response.success) {
                            // Loop through the product IDs and remove the corresponding rows/cards
                            tesw_productsToRemove.forEach(function (tesw_productID) {
                                // Remove the table row from the wishlist table
                                $(".tesw-wishlist").find("tr[data-product-id='" + tesw_productID + "']").remove();
                                });

                            // Check if there are any remaining products
                            if ($(".tesw-wishlist tr").length === 0) {
                                // Display the empty wishlist message
                                $(".tesw-wishlist").html("<p>" + tesw_ajax_object.empty_wishlist_message + "</p>");
                            }

                            // Reload the page after a delay of 1 second (1000 milliseconds)
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Display the error message
                            alert(tesw_ajax_object.remove_error);
                        }
                    },
                    complete: function () {
                        // Remove the loader class from the "Apply Action" button
                        $("#tesw-apply-button").removeClass("loading");
                    }
                });
            } else {
                // No products selected, show a message or take appropriate action
                alert(tesw_ajax_object.no_products_selected);
            }
        }
    });
    
})(jQuery);
