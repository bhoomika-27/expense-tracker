<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Please enter both email and password.';
    } else {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT id, name, password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['name'];

            header('Location: dashboard.php');
            exit;
        } else {
            // Same generic message whether the email or password was wrong,
            // so we don't reveal which emails are registered.
            $errors[] = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
?>

<div class="card auth-card p-4">
    <h3 class="mb-3 text-center">Login</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= h($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="login.php" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= h($email) ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <p class="text-center mt-3 mb-0">
        Don't have an account? <a href="register.php">Register here</a>
    </p>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
