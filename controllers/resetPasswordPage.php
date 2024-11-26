<?php
require_once __DIR__ . '/../../db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $updatePassword = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
            $updatePassword->bind_param("ss", $newPassword, $token);
            $updatePassword->execute();
            header('Location: success.php');
        }
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}
?>

<form method="POST">
    <div class="input-wrapper">
        <span>New Password* :</span>
        <input type="password" name="password" placeholder="New Password" required />
        <span>Confirm Password* :</span>
        <input type="password" name="confirmPassword" placeholder="Confirm Password" required />
        <button type="submit">Submit</button>
    </div>
</form>
<script>
    // Confirm Password Validation
    document.querySelector("form").addEventListener("submit", function (e) {
        const password = document.querySelector('input[name="password"]').value;
        const confirmPassword = document.querySelector('input[name="confirmPassword"]').value;
        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            e.preventDefault();
        }
    });
</script>