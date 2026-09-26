<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$u' AND password='$p'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header("Location: dashboard.php");
    } else {
        $error = "Username atau Password salah!";
    }
}

if (isset($_POST['guest'])) {
    $_SESSION['user'] = 'Pengunjung';
    $_SESSION['role'] = 'guest';
    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <title>Login - PestGuard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url(img/bg_sawah.png) no-repeat;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Tema Box (Konsisten dengan Dashboard) */
        .container {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #eee;
            width: 350px;
            text-align: center;
        }

        .logo-img {
            width: 60px;
            margin-bottom: 10px;
        }

        h2 {
            color: #2ecc71;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            background: #fafafa;
        }

        input:focus {
            outline: none;
            border-color: #2ecc71;
            background: #fff;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-login {
            background: #27ae60;
            color: white;
        }

        .btn-login:hover {
            background: #219150;
        }

        .btn-guest {
            background: white;
            border: 1px solid #ccc;
            color: #666;
        }

        .btn-guest:hover {
            background: #f5f5f5;
            color: #333;
        }

        .alert {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 15px;
            background: #fadbd8;
            padding: 10px;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="logo.png" alt="Logo" class="logo-img">
        <h2>PestGuard</h2>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required autocomplete="off">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn btn-login">Masuk</button>
            <button type="submit" name="guest" class="btn btn-guest">Masuk sebagai Tamu</button>
        </form>

        <?php if (isset($error))
            echo "<div class='alert'>$error</div>"; ?>

        <p style="margin-top:20px; font-size:0.9rem; color:#666;">
            Belum punya akun? <a href="register.php"
                style="color:#27ae60; font-weight:bold; text-decoration:none;">Daftar disini</a>
        </p>
    </div>
</body>

</html>