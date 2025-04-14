<?php
$page_title = "Admin Dashboard";
require_once('../includes/functions.php');

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('You need to login as admin to access this page', 'danger');
    redirect('../login.php');
}

// Count total posts
$query = "SELECT COUNT(*) as count FROM posts";
$result = mysqli_query($conn, $query);
$posts_count = mysqli_fetch_assoc($result)['count'];

// Count published posts
$query = "SELECT COUNT(*) as count FROM posts WHERE published = 1";
$result = mysqli_query($conn, $query);
$published_posts = mysqli_fetch_assoc($result)['count'];

// Count categories
$query = "SELECT COUNT(*) as count FROM categories";
$result = mysqli_query($conn, $query);
$categories_count = mysqli_fetch_assoc($result)['count'];

// Get recent posts
$query = "SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 5";
$result = mysqli_query($conn, $query);
$recent_posts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recent_posts[] = $row;
}

// Include admin header
require_once('includes/admin_header.php');
?>

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="add_post.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Add New Post
        </a>
    </div>
    
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="dashboard-card primary">
                <div class="text-center">
                    <i class="fas fa-file-alt"></i>
                    <h3><?php echo $posts_count; ?></h3>
                    <p class="mb-0">Total Posts</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="dashboard-card success">
                <div class="text-center">
                    <i class="fas fa-check-circle"></i>
                    <h3><?php echo $published_posts; ?></h3>
                    <p class="mb-0">Published Posts</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="dashboard-card info">
                <div class="text-center">
                    <i class="fas fa-folder"></i>
                    <h3><?php echo $categories_count; ?></h3>
                    <p class="mb-0">Categories</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Content Row -->
    <div class="row">
        <!-- Recent Posts -->
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title m-0 font-weight-bold">Recent Posts</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_posts)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No posts found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_posts as $post): ?>
                                        <tr>
                                            <td>
                                                <a href="edit_post.php?id=<?php echo $post['id']; ?>">
                                                    <?php echo htmlspecialchars($post['title']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($post['username']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                                            <td>
                                                <?php if ($post['published']): ?>
                                                    <span class="badge bg-success">Published</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Draft</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="posts.php" class="btn btn-sm btn-primary">View All Posts</a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title m-0 font-weight-bold">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="add_post.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> New Post
                        </a>
                        <a href="categories.php" class="btn btn-success">
                            <i class="fas fa-folder-plus me-1"></i> Manage Categories
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/admin_footer.php'); ?> 