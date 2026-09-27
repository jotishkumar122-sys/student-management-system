<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_login();

$q = trim($_GET['q'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 8;
$offset = ($page - 1) * $perPage;

if ($q !== '') {
    $countStmt = $pdo->prepare(
        "SELECT COUNT(*) FROM students WHERE full_name LIKE ? OR email LIKE ? OR course LIKE ?"
    );
    $like = "%{$q}%";
    $countStmt->execute([$like, $like, $like]);
    $total = (int) $countStmt->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT * FROM students WHERE full_name LIKE :like1 OR email LIKE :like2 OR course LIKE :like3
         ORDER BY created_at DESC LIMIT :lim OFFSET :off"
    );
    $stmt->bindValue(':like1', $like);
    $stmt->bindValue(':like2', $like);
    $stmt->bindValue(':like3', $like);
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $total = (int) $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
    $stmt = $pdo->prepare('SELECT * FROM students ORDER BY created_at DESC LIMIT :lim OFFSET :off');
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
}

$students = $stmt->fetchAll();
$totalPages = max(1, (int) ceil($total / $perPage));

$base = '..';
$pageTitle = 'Students';
require __DIR__ . '/../includes/header.php';
?>

<div class="container">
  <div class="page-head">
    <div>
      <h1>Students</h1>
      <p class="muted"><?= $total ?> total record<?= $total === 1 ? '' : 's' ?></p>
    </div>
    <?php if (is_admin()): ?>
      <a href="add.php" class="btn btn-primary">+ Add Student</a>
    <?php endif; ?>
  </div>

  <?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">Student added successfully.</div>
  <?php elseif (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Student record deleted.</div>
  <?php endif; ?>

  <form method="GET" class="search-bar">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search by name, email, or course...">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if ($q !== ''): ?><a href="list.php" class="btn btn-outline">Clear</a><?php endif; ?>
  </form>

  <div class="card">
    <?php if (empty($students)): ?>
      <p class="muted">No students found<?= $q !== '' ? " for “" . e($q) . "”" : '' ?>.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Name</th><th>Email</th><th>Phone</th><th>Course</th><th>Status</th><th>Enrolled</th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $s): ?>
              <tr>
                <td><?= e($s['full_name']) ?></td>
                <td><?= e($s['email']) ?></td>
                <td><?= e($s['phone']) ?></td>
                <td><?= e($s['course']) ?></td>
                <td><span class="badge badge-<?= e($s['status']) ?>"><?= e(ucfirst($s['status'])) ?></span></td>
                <td><?= e($s['enrollment_date']) ?></td>
                <td class="row-actions">
                  <a href="view.php?id=<?= $s['id'] ?>" class="link">View</a>
                  <?php if (is_admin()): ?>
                    <a href="edit.php?id=<?= $s['id'] ?>" class="link">Edit</a>
                    <form action="delete.php" method="POST" class="inline-form" data-confirm="Delete this student record?">
                      <input type="hidden" name="id" value="<?= $s['id'] ?>">
                      <button type="submit" class="link link-danger">Delete</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a href="?q=<?= urlencode($q) ?>&page=<?= $p ?>"
               class="page-link <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
