<?php
require_once('includes/functions.php');

// Get category by slug
$category_slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($category_slug)) {
    redirect('blog.php');
}

// Get category information
global $conn;
$query = "SELECT * FROM categories WHERE slug = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $category_slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    setFlashMessage('Kategori bulunamadı', 'danger');
    redirect('blog.php');
}

$category = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Pagination
$posts_per_page = 6; // Same as blog page for consistency
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $posts_per_page;

// Get total posts count in this category for pagination
$count_query = "SELECT COUNT(*) as total FROM posts p 
                JOIN post_category pc ON p.id = pc.post_id 
                WHERE pc.category_id = ? AND p.published = 1";
$count_stmt = mysqli_prepare($conn, $count_query);
mysqli_stmt_bind_param($count_stmt, "i", $category['id']);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_row = mysqli_fetch_assoc($count_result);
$total_posts = $count_row['total'];
$total_pages = ceil($total_posts / $posts_per_page);
mysqli_stmt_close($count_stmt);

// Get posts for current page in this category
$posts_query = "SELECT p.*, u.username 
                FROM posts p 
                JOIN post_category pc ON p.id = pc.post_id 
                JOIN users u ON p.user_id = u.id 
                WHERE pc.category_id = ? AND p.published = 1 
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
$posts_stmt = mysqli_prepare($conn, $posts_query);
mysqli_stmt_bind_param($posts_stmt, "iii", $category['id'], $posts_per_page, $offset);
mysqli_stmt_execute($posts_stmt);
$posts_result = mysqli_stmt_get_result($posts_stmt);

$posts = [];
while ($row = mysqli_fetch_assoc($posts_result)) {
    $posts[] = $row;
}
mysqli_stmt_close($posts_stmt);

// Get all categories for sidebar
$categories = getCategories();

// Get recent posts for sidebar
$recent_posts_query = "SELECT * FROM posts WHERE published = 1 ORDER BY created_at DESC LIMIT 5";
$recent_posts_result = mysqli_query($conn, $recent_posts_query);
$recent_posts = [];
while ($row = mysqli_fetch_assoc($recent_posts_result)) {
    $recent_posts[] = $row;
}

// Random placeholder images
$placeholder_images = [
    'https://source.unsplash.com/random/600x400/?coding',
    'https://source.unsplash.com/random/600x400/?technology',
    'https://source.unsplash.com/random/600x400/?programming',
    'https://source.unsplash.com/random/600x400/?computer',
    'https://source.unsplash.com/random/600x400/?web',
    'https://source.unsplash.com/random/600x400/?developer'
];

$page_title = $category['name'] . " - Kategorisi";
require_once('includes/header.php');
?>

<div class="container py-5">
    <div class="row">
        <!-- Ana İçerik -->
        <div class="col-lg-8">
            <div class="mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                        <li class="breadcrumb-item"><a href="blog.php">Blog</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($category['name']); ?></li>
                    </ol>
                </nav>
            </div>
            
            <div class="category-header mb-4">
                <h2 class="mb-2"><?php echo htmlspecialchars($category['name']); ?></h2>
                <?php if (!empty($category['description'])): ?>
                    <p class="text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
                <?php endif; ?>
                <div class="badge bg-primary"><?php echo $total_posts; ?> yazı</div>
            </div>
            
            <?php if (empty($posts)): ?>
                <div class="alert alert-info">Bu kategoride henüz blog yazısı bulunmamaktadır.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <?php if ($post['featured_image']): ?>
                                    <img src="assets/images/<?php echo $post['featured_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="<?php echo $placeholder_images[array_rand($placeholder_images)]; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>" style="height: 200px; object-fit: cover;">
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h3 class="card-title h5"><?php echo htmlspecialchars($post['title']); ?></h3>
                                    
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-calendar-alt me-1"></i> <?php echo date('d F Y', strtotime($post['created_at'])); ?>
                                        <i class="fas fa-user ms-2 me-1"></i> <?php echo htmlspecialchars($post['username']); ?>
                                    </div>
                                    
                                    <?php 
                                    // Get post categories
                                    $post_categories = getPostCategories($post['id']);
                                    if (!empty($post_categories)):
                                    ?>
                                    <div class="mb-2">
                                        <?php foreach ($post_categories as $cat): ?>
                                            <a href="category.php?slug=<?php echo $cat['slug']; ?>" class="badge <?php echo $cat['id'] == $category['id'] ? 'bg-primary' : 'bg-secondary'; ?> text-decoration-none me-1"><?php echo htmlspecialchars($cat['name']); ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <p class="card-text"><?php echo substr(strip_tags($post['content']), 0, 120); ?>...</p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <a href="post.php?slug=<?php echo $post['slug']; ?>" class="btn btn-outline-primary btn-sm">Devamını Oku</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?slug=<?php echo $category_slug; ?>&page=<?php echo $page - 1; ?>" tabindex="-1" <?php echo $page <= 1 ? 'aria-disabled="true"' : ''; ?>>Önceki</a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?slug=<?php echo $category_slug; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?slug=<?php echo $category_slug; ?>&page=<?php echo $page + 1; ?>" <?php echo $page >= $total_pages ? 'aria-disabled="true"' : ''; ?>>Sonraki</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Kenar Çubuğu -->
        <div class="col-lg-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-search me-2"></i>Blog'da Ara</h5>
                </div>
                <div class="card-body">
                    <form action="search.php" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Arama..." name="q" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-folder me-2"></i>Kategoriler</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($categories)): ?>
                        <p class="text-muted">Henüz kategori bulunmamaktadır.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($categories as $cat): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center <?php echo $cat['id'] == $category['id'] ? 'active' : ''; ?>">
                                    <a href="category.php?slug=<?php echo $cat['slug']; ?>" class="text-decoration-none <?php echo $cat['id'] == $category['id'] ? 'text-white' : 'text-dark'; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </a>
                                    <?php 
                                    // Count posts in this category
                                    $cat_count_query = "SELECT COUNT(*) as count FROM post_category WHERE category_id = " . $cat['id'];
                                    $cat_count_result = mysqli_query($conn, $cat_count_query);
                                    $cat_count_data = mysqli_fetch_assoc($cat_count_result);
                                    ?>
                                    <span class="badge <?php echo $cat['id'] == $category['id'] ? 'bg-white text-primary' : 'bg-primary'; ?> rounded-pill"><?php echo $cat_count_data['count']; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-clock me-2"></i>Son Yazılar</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_posts)): ?>
                        <p class="text-muted">Henüz yazı bulunmamaktadır.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recent_posts as $recent): ?>
                                <li class="list-group-item">
                                    <a href="post.php?slug=<?php echo $recent['slug']; ?>" class="text-decoration-none">
                                        <div class="row g-0">
                                            <div class="col-3">
                                                <?php if ($recent['featured_image']): ?>
                                                    <img src="assets/images/<?php echo $recent['featured_image']; ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($recent['title']); ?>" style="height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <img src="<?php echo $placeholder_images[array_rand($placeholder_images)]; ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($recent['title']); ?>" style="height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-9">
                                                <p class="mb-0 small fw-bold"><?php echo htmlspecialchars($recent['title']); ?></p>
                                                <p class="text-muted smaller mb-0"><?php echo date('d F Y', strtotime($recent['created_at'])); ?></p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?> 