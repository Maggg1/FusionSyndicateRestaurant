<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Ensure cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Calculate total price
$cart = $_SESSION['cart'];
$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// When user submits the form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_payment'])) {
    // Get selected values
    $bank = $_POST['bank'];
    $payment_method = $_POST['payment_method'];

    // Save to session for use in next pages
    $_SESSION['selected_bank'] = $bank;
    $_SESSION['selected_payment_method'] = $payment_method;
    $_SESSION['total_price'] = $total_price;

    // Redirect user to the correct payment method
    if ($payment_method === "Online Payment") {
        header("Location: Bank_Payment_Gateway.php");
        exit();
    } elseif ($payment_method === "Credit Card") {
        header("Location: Card_Method.php");
        exit();
    } else {
        // Cash on delivery - save purchase immediately
        include("classes/connectDb.php");
        $db = new DataBase();
        $conn = $db->connect();

        $user_id = $_SESSION['id'];
        $invoice_number = uniqid();
        $stmt = $conn->prepare("INSERT INTO purchases (user_id, item_name, price, quantity, payment_method, invoice_number) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($cart as $item) {
            $stmt->bind_param("isdiss", $user_id, $item['name'], $item['price'], $item['quantity'], $payment_method, $invoice_number);
            $stmt->execute();
        }

        $stmt->close();
        $conn->close();

        unset($_SESSION['cart']);
        $_SESSION['invoice_number'] = $invoice_number;

        header("Location: payment_success.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
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
        .payment-form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        select, button {
            margin-top: 5px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .confirm-button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            text-align: center;
            width: 100%;
            margin-top: 20px;
        }
        .confirm-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="payment-container">
    <h2>Payment Gateway</h2>
    <form method="POST" class="payment-form" id="paymentForm">
        <label for="payment_method">Select Payment Method:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="Cash On Delivery">Cash On Delivery</option>
            <option value="Credit Card">Credit/Debit Card</option>
            <option value="Online Payment">Online Payment</option>
        </select>
        
        
		<div id="bankSelection">
		<label for="bank">Select Your Bank:</label>
		<select name="bank" id="bank" required>
			<option value="Awesome Bank">Awesome Bank</option>
			<option value="Mega Bank">Mega Bank</option>
			<option value="Super Bank">Super Bank</option>
			<option value="Trust Bank">Trust Bank</option>
		</select>
		</div>


        <label>Total Price: $<?= number_format($total_price, 2) ?></label>
		<p>Customer need to pay at rider when using <strong>Cash On Delivery</strong> Method</p>
        <button type="submit" name="confirm_payment" class="confirm-button">Confirm Payment</button>
		<button type="button" onclick="window.location.href='cart.php'" class="confirm-button" style="background-color: #dc3545; margin-top: 10px;">Back</button>
    </form>
</div>

<!-- JavaScript handler -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const paymentMethodSelect = document.getElementById("payment_method");
        const bankSelection = document.getElementById("bankSelection");
        const bankInput = document.getElementById("bank");

        function toggleBankSelection() {
            if (paymentMethodSelect.value === "Cash On Delivery") {
                bankSelection.style.display = "none";
                bankInput.removeAttribute("required");
            } else {
                bankSelection.style.display = "block";
                bankInput.setAttribute("required", "required");
            }
        }

        // Initial check
        toggleBankSelection();

        // Add change listener
        paymentMethodSelect.addEventListener("change", toggleBankSelection);
    });
</script>
</body>
</html>
