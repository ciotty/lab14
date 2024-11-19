<?php
require 'vendor/autoload.php';  // Include Stripe PHP library

// Set your secret key. Replace with your actual secret key.
$stripe = new \Stripe\StripeClient('sk_test_51QKGo5FkJhB0o5wDOhD6nneoAfgcTAPgjNpwrNd95vmFzSqqBC7w57iZiNh2vUgOzyq7ZgPv8EI8hxRVIPTS2uXE00vzAt3IUv');

// Initialize variables for error and success messages
$error_message = '';
$success_message = '';

// Define available products with their price IDs
$products = [
    'product_1' => [
        'name' => 'Product 1',
        'price_id' => 'price_1MoC3TLkdIwHu7ixcIbKelAC',
    ],
    'product_2' => [
        'name' => 'Product 2',
        'price_id' => 'price_1MoC3TLkdIwHu7ixcIvabD1Q',  // Replace with actual price ID
    ],
    'product_3' => [
        'name' => 'Product 3',
        'price_id' => 'price_1MoC3TLkdIwHu7ixcIgFZy8W',  // Replace with actual price ID
    ]
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get selected products and quantity
    $selected_products = $_POST['products']; // Products selected by the user

    // Validate selection
    if (empty($selected_products)) {
        $error_message = "You must select at least one product.";
    } else {
        try {
            // Prepare line items for the payment link
            $line_items = [];
            foreach ($selected_products as $product_key) {
                if (isset($products[$product_key])) {
                    // Add each product to the line items array
                    $line_items[] = [
                        'price' => $products[$product_key]['price_id'],
                        'quantity' => 1,  // Set quantity to 1 for simplicity (can be dynamic if needed)
                    ];
                }
            }

            // Create the payment link
            $payment_link = $stripe->paymentLinks->create([
                'line_items' => $line_items,
            ]);

            // Success: Display the payment link URL
            $success_message = "Payment link created successfully! <a href='" . $payment_link->url . "' target='_blank'>Click here to pay</a>";
        } catch (Exception $e) {
            $error_message = "Error creating payment link: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Payment Link</title>
</head>
<body>
    <h1>Create a Payment Link</h1>

    <!-- Display success or error messages -->
    <?php if ($success_message): ?>
        <div style="color: green;"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div style="color: red;"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Form to select products and generate payment link -->
    <form method="POST" action="">
        <label>Select Products:</label><br>
        <?php foreach ($products as $key => $product): ?>
            <input type="checkbox" name="products[]" value="<?php echo $key; ?>"> <?php echo htmlspecialchars($product['name']); ?><br>
        <?php endforeach; ?>
        <br>

        <button type="submit">Generate Payment Link</button>
    </form>
</body>
</html>
