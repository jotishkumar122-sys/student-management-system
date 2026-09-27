<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (is_logged_in()) {
    header('Location: ../dashboard.php');
    exit;
}

$errors = [];
$old = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $old['name']  = $name;
    $old['email'] = $email;

    if ($name === '' || strlen($name) < 2) {
        $errors['name'] = 'Please enter your full name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors['email'] = 'An account with this email already exists.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)'
        );
        // Everyone who self-registers is a 'user'; promote to admin via SQL (see sql/schema.sql).
        $stmt->execute([$name, $email, $hash, 'user']);
        header('Location: login.php?registered=1');
        exit;
    }
}

$base = '..';
$pageTitle = 'Register';
require __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrap">
  <div class="auth-card">
    <h1>Create an account</h1>
    <p class="muted">Register to access the Student Management System.</p>

    <form method="POST" novalidate id="registerForm">
      <div class="form-group <?= isset($errors['name']) ? 'error' : '' ?>">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?= e($old['name']) ?>" placeholder="Your full name">
        <?php if (isset($errors['name'])): ?><div class="error-msg"><?= e($errors['name']) ?></div><?php endif; ?>
      </div>

      <div class="form-group <?= isset($errors['email']) ? 'error' : '' ?>">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" placeholder="you@example.com">
        <?php if (isset($errors['email'])): ?><div class="error-msg"><?= e($errors['email']) ?></div><?php endif; ?>
      </div>

      <div class="form-group <?= isset($errors['password']) ? 'error' : '' ?>">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="At least 6 characters">
        <?php if (isset($errors['password'])): ?><div class="error-msg"><?= e($errors['password']) ?></div><?php endif; ?>
      </div>

      <div class="form-group <?= isset($errors['confirm_password']) ? 'error' : '' ?>">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password">
        <?php if (isset($errors['confirm_password'])): ?><div class="error-msg"><?= e($errors['confirm_password']) ?></div><?php endif; ?>
      </div>

      <button type="submit" class="btn btn-primary btn-block" data-loading-text="Creating account...">Register</button>
    </form>

    <p class="muted center">Already have an account? <a href="login.php">Log in</a></p>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
