<?php
require_once __DIR__ . '/../config/role_check.php';
requireRole(['Administrator']);
require_once __DIR__ . '/../models/Course.php';

$courseModel = new Course();
$action = $_POST['action'] ?? '';
$redirect = '../views/courses.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect);
    exit;
}

if ($action === 'add' || $action === 'edit') {
    $courseName = trim($_POST['course_name'] ?? '');
    if ($courseName === '') {
        header('Location: ' . $redirect . '?error=required');
        exit;
    }

    if ($action === 'add') {
        $courseModel->create($_POST);
    } else {
        $courseModel->update((int) ($_POST['course_id'] ?? 0), $_POST);
    }
} elseif ($action === 'delete' && isset($_POST['course_id'])) {
    $courseModel->delete((int) $_POST['course_id']);
}

header('Location: ' . $redirect);
exit;
