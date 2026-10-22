<?php
require_once __DIR__ . '/../models/Customer.php';

$customerModel = new Customer();
$action = $_REQUEST['action'] ?? '';

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = filter_var($_POST['course_id'] ?? null, FILTER_VALIDATE_INT);
    if ($courseId === false || $courseId === null || $courseId < 1 || !$customerModel->courseExists($courseId)) {
        header('Location: ../views/customers.php?error=invalid_course');
        exit;
    }
    $customerModel->create($_POST);
    header('Location: ../views/customers.php');
    exit;
}

if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = filter_var($_POST['course_id'] ?? null, FILTER_VALIDATE_INT);
    if ($courseId === false || $courseId === null || $courseId < 1 || !$customerModel->courseExists($courseId)) {
        header('Location: ../views/customers.php?error=invalid_course');
        exit;
    }
    $customerModel->update($_POST['customer_id'], $_POST);
    header('Location: ../views/customers.php');
    exit;
}

if ($action === 'deactivate' && isset($_GET['id'])) {
    $customerModel->deactivate($_GET['id']);
    header('Location: ../views/customers.php');
    exit;
}