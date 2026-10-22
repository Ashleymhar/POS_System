<?php
require_once '../config/role_check.php';
requireRole(['Administrator']);
require_once '../models/Course.php';
include 'header.php';
include 'navbar.php';

$courseModel = new Course();
$courses = $courseModel->getAll();
$editCourse = null;
if (isset($_GET['edit_id'])) {
    $editCourse = $courseModel->getById((int) $_GET['edit_id']);
}
$viewCourse = null;
if (isset($_GET['view_id'])) {
    $viewCourse = $courseModel->getById((int) $_GET['view_id']);
}
?>
<main class="pos-content">
    <h2>Course Management</h2>

    <?php if (($_GET['error'] ?? '') === 'required'): ?>
        <p role="alert">Course name is required.</p>
    <?php endif; ?>

    <form action="../controllers/CourseController.php" method="POST">
        <input type="hidden" name="action" value="<?= $editCourse ? 'edit' : 'add' ?>">
        <?php if ($editCourse): ?>
            <input type="hidden" name="course_id" value="<?= (int) $editCourse['course_id'] ?>">
        <?php endif; ?>
        <label for="course_name">Course Name:</label>
        <input id="course_name" type="text" name="course_name" maxlength="120"
               value="<?= htmlspecialchars($editCourse['course_name'] ?? '') ?>" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="3"><?= htmlspecialchars($editCourse['description'] ?? '') ?></textarea><br>

        <button type="submit"><?= $editCourse ? 'Update Course' : 'Add Course' ?></button>
        <?php if ($editCourse): ?>
            <a href="courses.php">Cancel</a>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>Course Name</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        <?php foreach ($courses as $course): ?>
        <tr>
            <td><?= htmlspecialchars($course['course_name']) ?></td>
            <td><?= nl2br(htmlspecialchars($course['description'] ?? '')) ?></td>
            <td>
                <a href="?view_id=<?= (int) $course['course_id'] ?>">View</a> |
                <a href="?edit_id=<?= (int) $course['course_id'] ?>">Edit</a>
                <form action="../controllers/CourseController.php" method="POST" style="display:inline; margin:0; padding:0; border:0;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="course_id" value="<?= (int) $course['course_id'] ?>">
                    <button type="submit" onclick="return confirm('Delete this course?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($courses)): ?>
        <tr><td colspan="3">No courses found.</td></tr>
        <?php endif; ?>
    </table>

    <?php if ($viewCourse): ?>
    <hr>
    <h3>Course Details</h3>
    <p><strong>Course Name:</strong> <?= htmlspecialchars($viewCourse['course_name']) ?></p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($viewCourse['description'] ?? '')) ?></p>
    <p><strong>Created:</strong> <?= htmlspecialchars($viewCourse['created_at']) ?></p>
    <a href="courses.php">Close details</a>
    <?php endif; ?>
</main>
<?php include 'footer.php'; ?>
