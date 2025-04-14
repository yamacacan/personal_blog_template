<?php
$page_title = "Manage Messages";
require_once('../includes/functions.php');
require_once('includes/admin_header.php');

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('You need to login as admin to access this page', 'danger');
    redirect('../login.php');
}

// Fetch messages with filtering
global $conn;
$is_read_filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$query = "SELECT * FROM messages";

if ($is_read_filter === 'read') {
    $query .= " WHERE is_read = 1";
} elseif ($is_read_filter === 'unread') {
    $query .= " WHERE is_read = 0";
}

$query .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

// Delete message
if (isset($_GET['delete'])) {
    $message_id = (int)$_GET['delete'];
    
    if (deleteMessage($message_id)) {
        setFlashMessage('Message deleted successfully', 'success');
    } else {
        setFlashMessage('Error deleting message: ' . mysqli_error($conn), 'danger');
    }
    redirect('messages.php');
}

// Mark message as read
if (isset($_GET['mark_read'])) {
    $message_id = (int)$_GET['mark_read'];
    
    if (markMessageAsRead($message_id)) {
        setFlashMessage('Message marked as read', 'success');
    } else {
        setFlashMessage('Error marking message as read: ' . mysqli_error($conn), 'danger');
    }
    redirect('messages.php');
}
?>

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Messages</h1>
    <a href="posts.php" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to Posts
    </a>
</div>

<div class="mb-3">
    <form method="GET" action="messages.php">
        <label for="filter" class="form-label">Filter Messages:</label>
        <select name="filter" id="filter" class="form-select" onchange="this.form.submit()">
            <option value="all" <?php echo $is_read_filter === 'all' ? 'selected' : ''; ?>>All Messages</option>
            <option value="read" <?php echo $is_read_filter === 'read' ? 'selected' : ''; ?>>Read Messages</option>
            <option value="unread" <?php echo $is_read_filter === 'unread' ? 'selected' : ''; ?>>Unread Messages</option>
        </select>
    </form>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No messages found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <tr>
                                <td><?php echo $message['id']; ?></td>
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                <td><?php echo htmlspecialchars($message['message']); ?></td>
                                <td>
                                    <?php echo $message['is_read'] ? '<span class="badge bg-success">Read</span>' : '<span class="badge bg-secondary">Unread</span>'; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($message['created_at'])); ?></td>
                                <td>
                                    <a href="messages.php?mark_read=<?php echo $message['id']; ?>" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Mark as Read">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="messages.php?delete=<?php echo $message['id']; ?>" class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="tooltip" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once('includes/admin_footer.php'); ?>