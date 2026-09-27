<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (is_logged_in()) {
    header('Location: ../dashboard.php');
    exit;
}

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $old['email'] = $email;

    if ($email === '' || $password === '') {
        $errors['form'] = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $errors['form'] = 'Invalid email or password.';
        } else {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role']  = $user['role'];
            header('Location: ../dashboard.php');
            exit;
        }
    }
}

$base = '..';
$pageTitle = 'Login';
require __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrap">
  <div class="auth-card">
    <h1>Welcome back</h1>
    <p class="muted">Log in to the Student Management System.</p>

    <?php if (isset($_GET['registered'])): ?>
      <div class="alert alert-success">Account created — please log in.</div>
    <?php endif; ?>
    <?php if (isset($errors['form'])): ?>
      <div class="alert alert-error"><?= e($errors['form']) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate id="loginForm">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" placeholder="you@example.com">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Your password">
      </div>
      <button type="submit" class="btn btn-primary btn-block" data-loading-text="Signing in...">Login</button>
    </form>

    <p class="muted center">Don't have an account? <a href="register.php">Register</a></p>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
