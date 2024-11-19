<?php
require 'vendor/autoload.php';  // Include the Stripe PHP library

// Set your secret key. Replace with your actual secret key.
$stripe = new \Stripe\StripeClient('sk_test_51QKGo5FkJhB0o5wDOhD6nneoAfgcTAPgjNpwrNd95vmFzSqqBC7w57iZiNh2vUgOzyq7ZgPv8EI8hxRVIPTS2uXE00vzAt3IUv');

// Initialize error and success messages
$error_message = '';
$success_message = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form input data
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    // Validate the input fields
    if (empty($name) || empty($email)) {
        $error_message = "Both fields are required!";
    } else {
        try {
            // Create a new customer on Stripe
            $customer = $stripe->customers->create([
                'name' => $name,
                'email' => $email,
            ]);

            // If customer creation is successful
            $success_message = "Customer created successfully! Customer ID: " . $customer->id;
        } catch (Exception $e) {
            // Handle errors from Stripe API
            $error_message = "Error creating customer: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Stripe Customer</title>
</head>
<body>
    <h1>Create a New Customer</h1>

    <!-- Display success or error messages -->
    <?php if ($success_message): ?>
        <div style="color: green;"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div style="color: red;"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Customer creation form -->
    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <button type="submit">Create Customer</button>
    </form>
</body>
</html>
