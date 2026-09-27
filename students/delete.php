<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: list.php?deleted=1');
exit;
