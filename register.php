<?php
include 'db.php';
$msg = "";

if (isset($_POST['register'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];
    $c = $_POST['confirm'];

    if ($p !== $c) {
        $msg = "<p style='color:red'>Password tidak cocok!</p>";
    } else {
        $check = $conn->query("SELECT * FROM users WHERE username='$u'");
        if ($check->num_rows > 0) {
            $msg = "<p style='color:red'>Username sudah dipakai.</p>";
        } else {
            $conn->query("INSERT INTO users (username, password, role) VALUES ('$u', '$p', 'petugas')");
            $msg = "<p style='color:green'>Berhasil! <a href='index.php'>Login sekarang</a></p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar - PestGuard</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: white; padding: 2.5rem; border-radius: 12px; width: 350px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #2980b9; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <img src="logo.png" style="width:60px;">
        <h2>Buat Akun</h2>
        <?php echo $msg; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm" placeholder="Ulangi Password" required>
            <button type="submit" name="register" class="btn">Daftar</button>
        </form>
        <p><a href="index.php" style="text-decoration:none; color:#2980b9;">Sudah punya akun? Login</a></p>
    </div>
</body>
</html>