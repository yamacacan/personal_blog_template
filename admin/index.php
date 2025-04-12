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

// Count users
$query = "SELECT COUNT(*) as count FROM users";
$result = mysqli_query($conn, $query);
$users_count = mysqli_fetch_assoc($result)['count'];

// Count comments
$query = "SELECT COUNT(*) as count FROM comments";
$result = mysqli_query($conn, $query);
$comments_count = mysqli_fetch_assoc($result)['count'];

// Count pending comments
$query = "SELECT COUNT(*) as count FROM comments WHERE approved = 0";
$result = mysqli_query($conn, $query);
$pending_comments = mysqli_fetch_assoc($result)['count'];

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
    <h1 class="mb-4">Dashboard</h1>
    
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="text-center">
                    <i class="fas fa-file-alt"></i>
                    <h3><?php echo $posts_count; ?></h3>
                    <p>Total Posts</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="text-center">
                    <i class="fas fa-folder"></i>
                    <h3><?php echo $categories_count; ?></h3>
                    <p>Categories</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="text-center">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $users_count; ?></h3>
                    <p>Users</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="text-center">
                    <i class="fas fa-comments"></i>
                    <h3><?php echo $comments_count; ?></h3>
                    <p>Comments</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Content Row -->
    <div class="row">
        <!-- Recent Posts -->
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title m-0">Recent Posts</h5>
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
                                                    <?php echo $post['title']; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $post['username']; ?></td>
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
        </div>
        
        <!-- Quick Stats -->
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title m-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-4">
                                <h6>Published Posts</h6>
                                <h2 class="text-primary"><?php echo $published_posts; ?></h2>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $posts_count > 0 ? ($published_posts / $posts_count * 100) : 0; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-4">
                                <h6>Pending Comments</h6>
                                <h2 class="text-warning"><?php echo $pending_comments; ?></h2>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $comments_count > 0 ? ($pending_comments / $comments_count * 100) : 0; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <h6 class="mt-4">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="add_post.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> New Post
                        </a>
                        <a href="add_category.php" class="btn btn-secondary">
                            <i class="fas fa-folder-plus me-1"></i> New Category
                        </a>
                        <a href="comments.php?filter=pending" class="btn btn-warning">
                            <i class="fas fa-comment me-1"></i> Moderate Comments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/admin_footer.php'); ?> 