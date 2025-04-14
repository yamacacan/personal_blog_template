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

// Get all posts with pagination
$posts_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $posts_per_page;

// Get total posts count for pagination
$published_only = ($filter == 'published');
$draft_only = ($filter == 'drafts');

// Count total posts based on filter
global $conn;
if ($filter == 'published') {
    $count_query = "SELECT COUNT(*) as total FROM posts WHERE published = 1";
} elseif ($filter == 'drafts') {
    $count_query = "SELECT COUNT(*) as total FROM posts WHERE published = 0";
} else {
    $count_query = "SELECT COUNT(*) as total FROM posts";
}
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_posts = $count_row['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts using custom query based on filter
if ($filter == 'published') {
    $query = "SELECT p.*, u.username 
              FROM posts p 
              JOIN users u ON p.user_id = u.id 
              WHERE p.published = 1
              ORDER BY p.created_at DESC 
              LIMIT $posts_per_page OFFSET $offset";
} elseif ($filter == 'drafts') {
    $query = "SELECT p.*, u.username 
              FROM posts p 
              JOIN users u ON p.user_id = u.id 
              WHERE p.published = 0
              ORDER BY p.created_at DESC 
              LIMIT $posts_per_page OFFSET $offset";
} else {
    $query = "SELECT p.*, u.username 
              FROM posts p 
              JOIN users u ON p.user_id = u.id 
              ORDER BY p.created_at DESC 
              LIMIT $posts_per_page OFFSET $offset";
}
$result = mysqli_query($conn, $query);

$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}

// Delete post
if (isset($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    
    // Check if post exists
    $post = getPost($post_id, false, true);
    
    if ($post) {
        // Delete associated records first
        $stmt = mysqli_prepare($conn, "DELETE FROM post_category WHERE post_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $post_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Then delete the post
        $delete_query = "DELETE FROM posts WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $post_id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            setFlashMessage('Post deleted successfully', 'success');
        } else {
            setFlashMessage('Error deleting post: ' . mysqli_error($conn), 'danger');
        }
        
        mysqli_stmt_close($delete_stmt);
    } else {
        setFlashMessage('Post not found', 'danger');
    }
    
    redirect('posts.php');
}

require_once('includes/admin_header.php');
?>

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Posts</h1>
    <a href="add_post.php" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-1"></i> Add New Post
    </a>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="btn-group" role="group">
                    <a href="posts.php" class="btn btn-<?php echo $filter == 'all' ? 'primary' : 'outline-primary'; ?>">
                        All <span class="badge bg-secondary ms-1"><?php echo $total_posts; ?></span>
                    </a>
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
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="card-title m-0 font-weight-bold">All Posts<?php echo $filter != 'all' ? ' - ' . ucfirst($filter) : ''; ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
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
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($post['username']); ?></td>
                                <td>
                                    <?php
                                    // Get categories for this post using existing function
                                    $post_categories = getPostCategories($post['id']);
                                    $category_names = [];
                                    
                                    foreach ($post_categories as $category) {
                                        $category_names[] = htmlspecialchars($category['name']);
                                    }
                                    
                                    echo !empty($category_names) ? implode(', ', $category_names) : 'Uncategorized';
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
                    
                    <?php
                    // Limit shown pages
                    $start_page = max(1, min($page - 2, $total_pages - 4));
                    $end_page = min($total_pages, max($page + 2, 5));
                    
                    // Show first page if not included in range
                    if ($start_page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=1' . ($filter != 'all' ? '&filter=' . $filter : '') . '">1</a></li>';
                        if ($start_page > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    // Display page numbers
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">';
                        echo '<a class="page-link" href="?page=' . $i . ($filter != 'all' ? '&filter=' . $filter : '') . '">' . $i . '</a>';
                        echo '</li>';
                    }
                    
                    // Show last page if not included in range
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . ($filter != 'all' ? '&filter=' . $filter : '') . '">' . $total_pages . '</a></li>';
                    }
                    ?>
                    
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