<?php
require 'vendor/autoload.php';  // Include the Stripe PHP library

// Initialize Stripe client with your secret API key
$stripe = new \Stripe\StripeClient('sk_test_51QKGo5FkJhB0o5wDOhD6nneoAfgcTAPgjNpwrNd95vmFzSqqBC7w57iZiNh2vUgOzyq7ZgPv8EI8hxRVIPTS2uXE00vzAt3IUv');

// Initialize variables for error and success messages
$error_message = '';
$success_message = '';

// Fetch the customers and products from Stripe
$customers = $stripe->customers->all();  // Get a list of customers
$products = $stripe->products->all();   // Get a list of products

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer'];  // Get the selected customer ID
    $selected_products = $_POST['products'];  // Get the selected products

    // Add line items to the invoice for each selected product
    foreach ($selected_products as $product_id) {
        try {
            // Fetch product details
            $product = $stripe->products->retrieve($product_id);

            // Assuming price information is available in product metadata (price stored in cents)
            $product_price = isset($product->metadata['price']) ? (int)$product->metadata['price'] : 0;
            
            if ($product_price > 0) {
                // Create an invoice item for each selected product
                $stripe->invoiceItems->create([
                    'customer' => $customer_id,
                    'price_data' => [
                        'currency' => 'usd',  // Assuming USD, change as needed
                        'product' => $product_id,
                        'unit_amount' => $product_price,
                    ],
                ]);
            }
        } catch (Exception $e) {
            $error_message = "Error creating invoice item: " . $e->getMessage();
            break; // Stop processing if any error occurs
        }
    }

    // Create the invoice after adding all the line items
    if (empty($error_message)) {
        try {
            $invoice = $stripe->invoices->create([
                'customer' => $customer_id,
            ]);
            $invoice->finalizeInvoice();  // Finalize the invoice to make it payable

            // Redirect to hosted invoice URL
            header('Location: ' . $invoice->hosted_invoice_url);
            exit;
        } catch (Exception $e) {
            $error_message = "Error creating invoice: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
</head>
<body>
    <h1>Create Invoice</h1>

    <!-- Display success or error messages -->
    <?php if ($success_message): ?>
        <div style="color: green;"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div style="color: red;"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Create invoice form -->
    <form method="POST" action="">
        <label>Select Customer:
            <select name="customer" required>
                <?php foreach ($customers->data as $customer): ?>
                    <option value="<?php echo $customer->id; ?>"><?php echo htmlspecialchars($customer->name); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Select Products:
            <?php foreach ($products->data as $product): ?>
                <input type="checkbox" name="products[]" value="<?php echo $product->id; ?>">
                <?php echo htmlspecialchars($product->name); ?><br>
            <?php endforeach; ?>
        </label><br><br>

        <button type="submit">Create Invoice</button>
    </form>
</body>
</html>
