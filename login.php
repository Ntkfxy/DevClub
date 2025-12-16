<?php
session_start();
if (isset($_SESSION['is_logged_in'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check Hardcoded Credentials
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $allowed_domain = '@webmail.npru.ac.th';

    if ($u === 'admin' . $allowed_domain && $p === '1234') {
        $_SESSION['is_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "รหัสผ่านผิด หรือรูปแบบอีเมลไม่ถูกต้อง";
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DevClub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #0d6efd 0%, #00d2ff 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            border-radius: 15px;
        }
    </style>
</head>

<body>
    <div class="card login-card p-5 shadow border-0">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">DevClub Admin</h2>
            <p class="text-muted">เข้าสู่ระบบ</p>
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Email (Admin)</label>
                <input type="email" name="username" class="form-control" placeholder="admin@webmail.npru.ac.th" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="1234" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>

</html>