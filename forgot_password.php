<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <title>Forgot Password</title>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row border rounded-5 p-3 bg-white shadow box-area">
            <div class="col-md-12 right-box">
                <div class="row align-items-center">
                    <div class="header-text mb-4">
                        <h2>Forgot Password</h2>
                        <p>Enter your email address to reset your password.</p>
                    </div>
                    <form action="forgot_password.php" method="post">
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email address" required>
                        </div>
                        <div class="input-group mb-3">
                            <button type="submit" name="forgot_password" class="btn btn-lg btn-success w-100 fs-6">Submit</button>
                        </div>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                    </form>
                </div>
            </div> 
        </div>
    </div>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["forgot_password"])) {
    $email = $_POST["email"];
    
    require_once "database.php";

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(50)); // Generate a secure random token
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expiry time

        $sql = "UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token;

        // Send the reset link to the user's email
        $subject = "Password Reset Request";
        $message = "Click on the following link to reset your password: " . $resetLink;
        $headers = "From: no-reply@yourdomain.com";

        if (mail($email, $subject, $message, $headers)) {
            $success = "Password reset link has been sent to your email.";
        } else {
            $error = "Failed to send the password reset link. Please try again later.";
        }
    } else {
        $error = "No account found with that email address.";
    }
}
?>
