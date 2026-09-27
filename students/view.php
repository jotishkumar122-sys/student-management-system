<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: list.php');
    exit;
}

$base = '..';
$pageTitle = $student['full_name'];
require __DIR__ . '/../includes/header.php';
?>

<div class="container narrow">
  <div class="page-head">
    <h1><?= e($student['full_name']) ?></h1>
    <?php if (is_admin()): ?>
      <a href="edit.php?id=<?= $student['id'] ?>" class="btn btn-primary">Edit</a>
    <?php endif; ?>
  </div>

  <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Student record updated.</div>
  <?php endif; ?>

  <div class="card detail-card">
    <div class="detail-row"><span class="detail-label">Email</span><span><?= e($student['email']) ?></span></div>
    <div class="detail-row"><span class="detail-label">Phone</span><span><?= e($student['phone']) ?></span></div>
    <div class="detail-row"><span class="detail-label">Course</span><span><?= e($student['course']) ?></span></div>
    <div class="detail-row">
      <span class="detail-label">Status</span>
      <span class="badge badge-<?= e($student['status']) ?>"><?= e(ucfirst($student['status'])) ?></span>
    </div>
    <div class="detail-row"><span class="detail-label">Enrollment Date</span><span><?= e($student['enrollment_date']) ?></span></div>
    <div class="detail-row"><span class="detail-label">Record Added</span><span><?= e($student['created_at']) ?></span></div>
  </div>

  <a href="list.php" class="btn btn-outline">&larr; Back to Students</a>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
