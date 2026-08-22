<?php
/* ==========================================================
   admin/reviews.php - Moderate movie reviews: view everything
   users have written and remove anything inappropriate.
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $del = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    redirect('reviews.php');
}

$reviews = $conn->query("
    SELECT r.*, u.full_name, m.title
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    JOIN movies m ON r.movie_id = m.movie_id
    ORDER BY r.created_at DESC
    LIMIT 300
");

include __DIR__ . '/admin_header.php';
?> <h1>Manage Reviews</h1> <div class="admin-table-scroll"> <table> <tr><th>Movie</th><th>User</th><th>Rating</th><th>Comment</th><th>Date</th><th>Actions</th></tr> <?php while ($r = $reviews->fetch_assoc()): ?> <tr> <td><?php echo clean($r['title']); ?></td> <td><?php echo clean($r['full_name']); ?></td> <td> <?php echo (int)$r['rating']; ?>/5</td> <td style="max-width:320px;"><?php echo nl2br(clean($r['comment'])); ?></td> <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td> <td> <a href="?delete=<?php echo $r['review_id']; ?>" onclick="return confirm('Delete this review?');" style="color:#ff5c5c;">Delete</a> </td> </tr> <?php endwhile; ?>
</table> </div> <?php include __DIR__ . '/admin_footer.php'; ?>