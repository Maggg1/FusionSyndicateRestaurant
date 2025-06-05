<?php
session_start();

// Redirect if no invoice number is set
if (!isset($_SESSION['invoice_number'])) {
    header("Location: index.php");
    exit();
}

$invoice_number = $_SESSION['invoice_number'];

// Clear invoice number after displaying (optional)
unset($_SESSION['invoice_number']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        .success-container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .success-message {
            font-size: 20px;
            color: green;
            font-weight: bold;
        }
        .invoice {
            margin-top: 10px;
            font-size: 18px;
        }
        .home-button {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .home-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h2 class="success-message">Order Successful!</h2>
        <p class="invoice">Your Invoice Number: <strong><?= htmlspecialchars($invoice_number) ?></strong></p>
        <a href="index.php" class="home-button">Back to Home</a>
		<a href="trackorder.php" class="home-button">Track Your Order</a>
    </div>
</body>
</html>
