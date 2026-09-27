<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
require_login();

$totalStudents = (int) $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$activeStudents = (int) $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn();
$newThisMonth = (int) $pdo->query(
    "SELECT COUNT(*) FROM students WHERE MONTH(enrollment_date) = MONTH(CURDATE()) AND YEAR(enrollment_date) = YEAR(CURDATE())"
)->fetchColumn();
$totalUsers = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

$recent = $pdo->query(
    'SELECT id, full_name, email, course, status, enrollment_date FROM students ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

$base = '.';
$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="container">

  <?php if (isset($_GET['error']) && $_GET['error'] === 'forbidden'): ?>
    <div class="alert alert-error">That action needs an admin account.</div>
  <?php endif; ?>

  <div class="page-head">
    <div>
      <h1>Dashboard</h1>
      <p class="muted">Welcome back, <?= e(current_user()['name']) ?>.</p>
    </div>
    <form action="students/list.php" method="GET" class="dash-search">
      <input type="text" name="q" placeholder="Search students by name or email...">
      <button type="submit" class="btn btn-primary">Search</button>
    </form>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <span class="stat-label">Total Students</span>
      <span class="stat-num"><?= $totalStudents ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Active Students</span>
      <span class="stat-num"><?= $activeStudents ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">New This Month</span>
      <span class="stat-num"><?= $newThisMonth ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">System Users</span>
      <span class="stat-num"><?= $totalUsers ?></span>
    </div>
  </div>

  <div class="card">
    <div class="card-head">
      <h2>Recent Registrations</h2>
      <a href="students/list.php" class="btn btn-outline btn-sm">View all</a>
    </div>

    <?php if (empty($recent)): ?>
      <p class="muted">No students registered yet.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Name</th><th>Email</th><th>Course</th><th>Status</th><th>Enrolled</th></tr>
          </thead>
          <tbody>
            <?php foreach ($recent as $s): ?>
              <tr>
                <td><a href="students/view.php?id=<?= $s['id'] ?>"><?= e($s['full_name']) ?></a></td>
                <td><?= e($s['email']) ?></td>
                <td><?= e($s['course']) ?></td>
                <td><span class="badge badge-<?= e($s['status']) ?>"><?= e(ucfirst($s['status'])) ?></span></td>
                <td><?= e($s['enrollment_date']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
