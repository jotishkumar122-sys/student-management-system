<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$errors = [];
$old = ['full_name' => '', 'email' => '', 'phone' => '', 'course' => '', 'status' => 'active', 'enrollment_date' => date('Y-m-d')];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $default) {
        $old[$key] = trim($_POST[$key] ?? $default);
    }

    if (strlen($old['full_name']) < 2) $errors['full_name'] = 'Please enter the student\'s full name.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email.';
    if (!preg_match('/^[0-9+\-\s]{7,20}$/', $old['phone'])) $errors['phone'] = 'Please enter a valid phone number.';
    if ($old['course'] === '') $errors['course'] = 'Please enter a course name.';
    if (!in_array($old['status'], ['active', 'inactive'], true)) $errors['status'] = 'Invalid status.';
    if ($old['enrollment_date'] === '') $errors['enrollment_date'] = 'Please pick an enrollment date.';

    if (empty($errors)) {
        $check = $pdo->prepare('SELECT id FROM students WHERE email = ?');
        $check->execute([$old['email']]);
        if ($check->fetch()) $errors['email'] = 'A student with this email already exists.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO students (full_name, email, phone, course, status, enrollment_date) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$old['full_name'], $old['email'], $old['phone'], $old['course'], $old['status'], $old['enrollment_date']]);
        header('Location: list.php?added=1');
        exit;
    }
}

$base = '..';
$pageTitle = 'Add Student';
require __DIR__ . '/../includes/header.php';
?>

<div class="container narrow">
  <h1>Add Student</h1>
  <div class="card">
    <form method="POST" novalidate id="studentForm">
      <div class="form-group <?= isset($errors['full_name']) ? 'error' : '' ?>">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" value="<?= e($old['full_name']) ?>">
        <?php if (isset($errors['full_name'])): ?><div class="error-msg"><?= e($errors['full_name']) ?></div><?php endif; ?>
      </div>

      <div class="form-row">
        <div class="form-group <?= isset($errors['email']) ? 'error' : '' ?>">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= e($old['email']) ?>">
          <?php if (isset($errors['email'])): ?><div class="error-msg"><?= e($errors['email']) ?></div><?php endif; ?>
        </div>
        <div class="form-group <?= isset($errors['phone']) ? 'error' : '' ?>">
          <label for="phone">Phone</label>
          <input type="text" id="phone" name="phone" value="<?= e($old['phone']) ?>" placeholder="03001234567">
          <?php if (isset($errors['phone'])): ?><div class="error-msg"><?= e($errors['phone']) ?></div><?php endif; ?>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group <?= isset($errors['course']) ? 'error' : '' ?>">
          <label for="course">Course</label>
          <input type="text" id="course" name="course" value="<?= e($old['course']) ?>" placeholder="BS Computer Science">
          <?php if (isset($errors['course'])): ?><div class="error-msg"><?= e($errors['course']) ?></div><?php endif; ?>
        </div>
        <div class="form-group">
          <label for="status">Status</label>
          <select id="status" name="status">
            <option value="active" <?= $old['status'] === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $old['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
      </div>

      <div class="form-group <?= isset($errors['enrollment_date']) ? 'error' : '' ?>">
        <label for="enrollment_date">Enrollment Date</label>
        <input type="date" id="enrollment_date" name="enrollment_date" value="<?= e($old['enrollment_date']) ?>">
        <?php if (isset($errors['enrollment_date'])): ?><div class="error-msg"><?= e($errors['enrollment_date']) ?></div><?php endif; ?>
      </div>

      <div class="form-actions">
        <a href="list.php" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary" data-loading-text="Saving...">Save Student</button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
