<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = 'Please enter your name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $pdo = getDbConnection();

        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with that email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);

            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['user_name'] = $name;

            header('Location: dashboard.php');
            exit;
        }
    }
}

$pageTitle = 'Register';
require __DIR__ . '/includes/header.php';
?>

<div class="card auth-card p-4">
    <h3 class="mb-3 text-center">Create an Account</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= h($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="register.php" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= h($name) ?>" required maxlength="100">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= h($email) ?>" required maxlength="150">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required minlength="8">
            <div class="form-text">At least 8 characters.</div>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>

    <p class="text-center mt-3 mb-0">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
