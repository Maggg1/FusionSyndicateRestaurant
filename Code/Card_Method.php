<?php
session_start();

// Redirect if cart or required session data is missing
if (!isset($_SESSION['id']) || !isset($_SESSION['cart']) || !isset($_SESSION['selected_bank']) || !isset($_SESSION['selected_payment_method'])) {
    header("Location: payment_gateway.php");
    exit();
}

include("classes/connectDb.php");
$db = new DataBase();
$conn = $db->connect();

$user_id = $_SESSION['id'];
$cart = $_SESSION['cart'];
$bank_name = $_SESSION['selected_bank'];
$payment_method = $_SESSION['selected_payment_method'];
$total_price = 0;
$invoice_number = uniqid();

// Get user name
$user_name = "Customer";
$stmt = $conn->prepare("SELECT first_name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name);
if ($stmt->fetch()) {
    $user_name = $first_name;
}
$stmt->close();

// If form is submitted, insert purchase and redirect
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_payment'])) {
    $stmt = $conn->prepare("INSERT INTO purchases (user_id, item_name, price, quantity, payment_method, invoice_number) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($cart as $item) {
        $stmt->bind_param("isdiss", $user_id, $item['name'], $item['price'], $item['quantity'], $payment_method, $invoice_number);
        $stmt->execute();
    }

    $stmt->close();
    unset($_SESSION['cart']);
    $_SESSION['invoice_number'] = $invoice_number;

    header("Location: payment_success.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Card Payment Gateway</title>
    <style>
        .payment-container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .payment-container h2 {
            text-align: center;
        }
        .payment-container p {
            font-size: 16px;
            margin: 10px 0;
        }
        .confirm-button {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .confirm-button:hover {
            background-color: #0056b3;
        }
        header h1 {
            text-align: center;
        }
    </style>
</head>
<body>
<header>
    <h1>Card Payment Gateway</h1>
</header>

<div class="payment-container">
    <h1><?= htmlspecialchars($bank_name) ?></h1>
    <h2>Card Details</h2>

    <p><strong>Name:</strong> <?= htmlspecialchars($user_name) ?></p>
	<pre><strong>Card No:</strong> 5899 6544 0187 601   <strong>Cv No:</strong> 984
	                   <strong>Exp Date:</strong> 2028</pre>
    <p><strong>Payment To:</strong> Fusion Syndicate Cafe</p>
    <p><strong>Total Payment:</strong> RM <?= number_format(array_reduce($cart, fn($sum, $i) => $sum + $i['price'] * $i['quantity'], 0), 2) ?></p>

    <form method="POST" class="payment-form" id="paymentForm">
		<input type="hidden" name="bank" value="<?= htmlspecialchars($bank_name) ?>">
		<input type="hidden" name="payment_method" value="<?= htmlspecialchars($payment_method) ?>">

		<button type="submit" name="confirm_payment" class="confirm-button">Make Payment</button>
		<button type="button" onclick="window.location.href='payment_gateway.php'" class="confirm-button" style="background-color: #dc3545; margin-top: 10px;">Back</button>
	</form>

</div>

</body>
</html>
