<?php
// Include the Stripe PHP library
require_once 'vendor/autoload.php';

// Set your Stripe secret key (replace with your own test/live secret key)
$stripe = new \Stripe\StripeClient('sk_test_51QKGo5FkJhB0o5wDOhD6nneoAfgcTAPgjNpwrNd95vmFzSqqBC7w57iZiNh2vUgOzyq7ZgPv8EI8hxRVIPTS2uXE00vzAt3IUv');

// Fetch the list of products from Stripe (with a limit of 3 products)
$products = $stripe->products->all(['limit' => 3]);

echo '<div style="display: flex; justify-content: center; flex-wrap: wrap;">'; // Start a flex container

// Loop through each product to fetch and display its details
foreach ($products->data as $product) {
    echo '<div style="text-align: center; margin: 20px; border: 1px solid #ccc; padding: 20px; width: 300px;">'; // Product container with center alignment

    // Display product name
    echo "<h2>" . htmlspecialchars($product->name) . "</h2>";
    echo "<p>" . htmlspecialchars($product->description) . "</p>";

    // Check if the product has images and display them
    if (!empty($product->images)) {
        foreach ($product->images as $imageUrl) {
            echo "<img src='" . htmlspecialchars($imageUrl) . "' alt='" . htmlspecialchars($product->name) . "' style='max-width: 100%; height: auto; margin-bottom: 20px;'><br>";
        }
    } else {
        echo "<p>No images available for this product.</p>";
    }

    // Fetch the prices for this product
    $prices = $stripe->prices->all(['product' => $product->id]);

    // Check if the product has any prices
    if (count($prices->data) > 0) {
        foreach ($prices->data as $price) {
            // Convert price from cents to dollars (for example, 2000 cents = 20.00 dollars)
            $priceAmount = $price->unit_amount / 100;  // Dividing by 100 to convert from cents to dollars
            $currency = strtoupper($price->currency);  // Convert currency to uppercase (e.g., USD)

            echo "<p><strong>Price: </strong>" . $priceAmount . " " . $currency . "</p>";
        }
    } else {
        echo "<p>No prices available for this product.</p>";
    }

    echo "</div>"; // End of product container
}

echo '</div>'; // End of the flex container
?>
