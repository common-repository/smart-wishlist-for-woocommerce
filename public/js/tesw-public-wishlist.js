(function ($) {
    'use strict';

    $(document).ready(function ($) {
        var teswViewWishlistButton = $(".tesw-view-wishlist-button");
        teswViewWishlistButton.show();
        
        function teswAddToWishlist(teswButton, teswProductID, teswUserID, teswWishlistName) {
            // Show loader on the button
            teswButton.addClass("loading");
        
            // Rest of the code for adding the product to the wishlist
            var teswUserData = {
                user_id: teswUserID,
            };
            var teswProductData = {
                product_id: teswProductID,
                name: teswButton.data("product-name"),
                price: teswButton.data("product-price"),
                // Add any other relevant product information here
            };
        
            // Make an AJAX request to add the product to the wishlist
            $.ajax({
                url: tesw_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'tesw_add_to_wishlist',
                    product_id: teswProductData.product_id,
                    user_id: teswUserData.user_id,
                    wishlist_name: teswWishlistName, // Pass the wishlist name in the AJAX request
                    teswnonce: tesw_ajax_object.nonce
                },
                beforeSend: function () {
                    // Show loader animation
                    teswButton.addClass("loading");
                },
                success: function (response) {
                    if (response.success) {
                        // Product added to wishlist
                        teswButton.addClass("tesw-added");
                        teswButton.removeClass("loading");
        
                        // Show the "View Wishlist" button immediately
                        teswViewWishlistButton.show();
        
                        // Show the product added message
                        var teswProductAddedMessage = $("<div class='tesw-product-added-message'>" + tesw_ajax_object.product_added_message + "</div>");
                        teswButton.after(teswProductAddedMessage);
        
                        // Remove the message after a certain duration (e.g., 3 seconds)
                        setTimeout(function () {
                            teswProductAddedMessage.remove();
                        }, 3000);
        
                        // Reload the page
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Handle the error if needed
                        teswButton.removeClass("loading"); // Remove the loading class in case of an error
                    }
                },
                error: function (error) {
                    // Handle the error if needed
                    alert(tesw_ajax_object.remove_error_wishlist);
                    teswButton.removeClass("loading"); // Remove the loading class in case of an error
                }
            });
        }
        
        $(document).on("click", ".tesw-wishlistify-button", function (e) {
            e.preventDefault(); // Prevent the default form submission
        
            var teswButton = $(this);
            var teswProductID = teswButton.data("product-id");
            var teswUserID = teswButton.data("user-id");
            var teswWishlistName = '';
        
            // Check if the user is logged in
            if (teswUserID === 0) {
                var teswUnloggedMessage = teswButton.siblings(".tesw-unlogged-message");
                teswUnloggedMessage.show();
        
                // Hide the message after a certain duration (e.g., 5 seconds)
                setTimeout(function () {
                    teswUnloggedMessage.hide();
                }, 3000);
        
                return false;
            }
        
            // Check if the button has the "added" class, indicating that the product is already added
            if (teswButton.hasClass("tesw-added")) {
                // Exit the function to prevent adding the product again
                return false;
            }
        
            // Check if wishlist collection is enabled
            var teswWishlistCollectionEnabled = true; // Modify this based on your logic
        
            if (teswWishlistCollectionEnabled) {
                // Get the wishlist name from the input box
                var teswWishlistNameInput = teswButton.siblings("input[name='tesw_new_wishlist_name']");
                if (teswWishlistNameInput.length > 0) {
                    teswWishlistName = teswWishlistNameInput.val().trim();
                }
            }
            // Call the teswAddToWishlist function
            teswAddToWishlist(teswButton, teswProductID, teswUserID, teswWishlistName);
        });
        
        // Share wishlist link
        $('.tesw-copy-link-share').on('click', function (event) {
            event.preventDefault();

            var teswWishlistLink = $(this).data('wishlist-link');

            // Create a temporary input element to copy the link
            var teswInputElement = $('<input>').val(teswWishlistLink).appendTo('body');

            // Select the text in the input element
            teswInputElement.select();

            try {
                // Use the Clipboard API to copy the text to the clipboard
                document.execCommand('copy');

                // Display the success message
                $('#tesw-copy-message').show();

                // Hide the success message after a few seconds (optional)
                setTimeout(function () {
                    $('#tesw-copy-message').hide();
                }, 3000);
            } catch (err) {
                // If copying fails, handle the error
                alert(tesw_ajax_object.copy_error_message + ': ' + err.message);
            } finally {
                // Remove the temporary input element
                teswInputElement.remove();
            }
        });

        // Function to handle adding selected products to the cart
        function teswAddSelectedProductsToCart() {
            var teswSelectedProductIDs = teswgetSelectedProductIDs();
            if (teswSelectedProductIDs.length === 0) {
                alert(tesw_ajax_object.no_products_select_add_into_cart);
                return;
            }

            $.ajax({
                url: tesw_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'tesw_add_multiple_to_cart',
                    productIDs: teswSelectedProductIDs,
                    teswmultinoncecart: tesw_ajax_object.nonce,
                },
                beforeSend: function () {
                    // Display loading spinner or any UI indication
                },
                success: function (response) {
                    // Handle the success response (updated cart data)
                    if (response && response.cart_count) {
                        // Update the cart count in the frontend (if required)
                        $('.cart-count').html(response.cart_count);
                    }
                    // Display success message
                    if (response && response.message) {
                        alert(response.message);
                    }
                    // Reload the page without visibly showing the refresh
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                },
                error: function (xhr, status, error) {
                  
                },
                complete: function () {
                },
            });
        }

        // Handle "Apply Action" button click
        $('#tesw-apply-button').on('click', function (e) {
            e.preventDefault();
            var action = $('.tesw-action-select').val();
            if (action === 'tesw-multiple-add-to-cart') {
                teswAddSelectedProductsToCart();
            } else if (action === 'remove') {
            }
        });

        // Function to get selected product IDs
        function teswgetSelectedProductIDs() {
            var teswSelectedProductIDs = [];
            $('.tesw-product-checkbox:checked').each(function () {
                var teswproductID = $(this).data('product-id');
                teswSelectedProductIDs.push(teswproductID);
            });
            return teswSelectedProductIDs;
        }
    });

})(jQuery);
