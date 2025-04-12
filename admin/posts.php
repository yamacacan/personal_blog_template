<?php
$page_title = "Manage Posts";
require_once('../includes/functions.php');

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('You need to login as admin to access this page', 'danger');
    redirect('../login.php');
}

// Filter by publish status
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where_clause = "";

if ($filter == 'published') {
    $where_clause = "WHERE p.published = 1";
} elseif ($filter == 'drafts') {
    $where_clause = "WHERE p.published = 0";
}

// Pagination
$posts_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $posts_per_page;

// Get total posts count for pagination
$count_query = "SELECT COUNT(*) as total FROM posts p $where_clause";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_posts = $count_row['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts
$query = "SELECT p.*, u.username 
          FROM posts p 
          JOIN users u ON p.user_id = u.id 
          $where_clause
          ORDER BY p.created_at DESC 
          LIMIT $posts_per_page OFFSET $offset";
$result = mysqli_query($conn, $query);

$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}

// Delete post
if (isset($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    
    // Delete associated records first
    mysqli_query($conn, "DELETE FROM post_category WHERE post_id = $post_id");
    mysqli_query($conn, "DELETE FROM comments WHERE post_id = $post_id");
    
    // Then delete the post
    $delete_query = "DELETE FROM posts WHERE id = $post_id";
    if (mysqli_query($conn, $delete_query)) {
        setFlashMessage('Post deleted successfully', 'success');
    } else {
        setFlashMessage('Error deleting post: ' . mysqli_error($conn), 'danger');
    }
    
    redirect('posts.php');
}

require_once('includes/admin_header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Posts</h1>
    <a href="add_post.php" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Add New Post
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <a href="posts.php" class="btn btn-<?php echo $filter == 'all' ? 'primary' : 'outline-primary'; ?>">All</a>
                    <a href="posts.php?filter=published" class="btn btn-<?php echo $filter == 'published' ? 'primary' : 'outline-primary'; ?>">Published</a>
                    <a href="posts.php?filter=drafts" class="btn btn-<?php echo $filter == 'drafts' ? 'primary' : 'outline-primary'; ?>">Drafts</a>
                </div>
            </div>
            <div class="col-md-6">
                <form action="search_posts.php" method="GET" class="d-flex">
                    <input type="text" name="q" class="form-control me-2" placeholder="Search posts...">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Posts Table -->
<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Categories</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No posts found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <a href="edit_post.php?id=<?php echo $post['id']; ?>">
                                        <?php echo $post['title']; ?>
                                    </a>
                                </td>
                                <td><?php echo $post['username']; ?></td>
                                <td>
                                    <?php
                                    // Get categories for this post
                                    $post_id = $post['id'];
                                    $cat_query = "SELECT c.name FROM categories c 
                                                  JOIN post_category pc ON c.id = pc.category_id 
                                                  WHERE pc.post_id = $post_id";
                                    $cat_result = mysqli_query($conn, $cat_query);
                                    $categories = [];
                                    
                                    while ($cat = mysqli_fetch_assoc($cat_result)) {
                                        $categories[] = $cat['name'];
                                    }
                                    
                                    echo !empty($categories) ? implode(', ', $categories) : 'Uncategorized';
                                    ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                                <td>
                                    <?php if ($post['published']): ?>
                                        <span class="badge bg-success">Published</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="table-action-buttons">
                                    <a href="../post.php?slug=<?php echo $post['slug']; ?>" class="btn btn-sm btn-outline-info" target="_blank" data-bs-toggle="tooltip" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="posts.php?delete=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="tooltip" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-4">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $filter != 'all' ? '&filter=' . $filter : ''; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link" aria-hidden="true">&laquo;</span>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $filter != 'all' ? '&filter=' . $filter : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $filter != 'all' ? '&filter=' . $filter : ''; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link" aria-hidden="true">&raquo;</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php require_once('includes/admin_footer.php'); ?> 