<?php
/* ==========================================================
   admin/users.php - Admin can view all registered visitors.
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

include __DIR__ . '/admin_header.php';
?> <h1>Registered Users</h1> <div class="admin-table-scroll"> <table class="admin-table-nowrap"> <tr><th>#</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Joined On</th></tr> <?php $i = 1; while ($u = $users->fetch_assoc()): ?> <tr> <td><?php echo $i++; ?></td> <td><?php echo clean($u['full_name']); ?></td> <td><?php echo clean($u['email']); ?></td> <td><?php echo clean($u['phone']); ?></td> <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td> </tr> <?php endwhile; ?>
</table> </div> <?php include __DIR__ . '/admin_footer.php'; ?>