<?php
// Expects $base ('.' or '..') and optional $pageTitle to be set before include.
$base = $base ?? '.';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>Student Management System</title>
<link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
<?php if ($user): ?>
<nav class="navbar">
  <div class="nav-inner">
    <a href="<?= $base ?>/dashboard.php" class="logo">SMS<span>.</span></a>
    <button class="hamburger" id="hamburger" aria-label="Menu"><span></span><span></span><span></span></button>
    <ul class="nav-links" id="navLinks">
      <li><a href="<?= $base ?>/dashboard.php">Dashboard</a></li>
      <li><a href="<?= $base ?>/students/list.php">Students</a></li>
      <?php if (is_admin()): ?>
        <li><a href="<?= $base ?>/students/add.php">Add Student</a></li>
      <?php endif; ?>
    </ul>
    <div class="nav-user">
      <span class="role-badge role-<?= e($user['role']) ?>"><?= e(ucfirst($user['role'])) ?></span>
      <span class="user-name"><?= e($user['name']) ?></span>
      <a href="<?= $base ?>/auth/logout.php" class="btn btn-outline btn-sm">Logout</a>
    </div>
  </div>
</nav>
<?php endif; ?>
<main class="page">
