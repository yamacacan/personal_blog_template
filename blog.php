<?php
$page_title = "Blog";
require_once('includes/functions.php');

// Pagination
$posts_per_page = 6; // Changed to 6 for better grid layout (3x2)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $posts_per_page;

// Get total posts count for pagination
$count_query = "SELECT COUNT(*) as total FROM posts WHERE published = 1";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_posts = $count_row['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts for current page
$posts = getPosts($posts_per_page, $offset);

// Get categories for sidebar
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

require_once('includes/header.php');
?>

<div class="container py-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <h2 class="mb-4">Blog Yazıları</h2>
            
            <div class="mb-4">
                <p>Yazılım, web geliştirme ve teknoloji konularında bilgi ve deneyimlerimi paylaştığım blog sayfama hoş geldiniz.</p>
            </div>
            
            <?php if (empty($posts)): ?>
                <div class="alert alert-info">Henüz blog yazısı bulunmamaktadır.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <?php if ($post['featured_image']): ?>
                                    <img src="assets/images/<?php echo $post['featured_image']; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="<?php echo $placeholder_images[array_rand($placeholder_images)]; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>" style="height: 200px; object-fit: cover;">
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h3 class="card-title h5"><?php echo $post['title']; ?></h3>
                                    
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-calendar-alt me-1"></i> <?php echo date('d F Y', strtotime($post['created_at'])); ?>
                                        <i class="fas fa-user ms-2 me-1"></i> <?php echo $post['username']; ?>
                                    </div>
                                    
                                    <?php 
                                    // Get post categories
                                    $post_categories = getPostCategories($post['id']);
                                    if (!empty($post_categories)):
                                    ?>
                                    <div class="mb-2">
                                        <?php foreach ($post_categories as $cat): ?>
                                            <span class="badge bg-secondary me-1"><?php echo $cat['name']; ?></span>
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
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>" tabindex="-1" <?php echo $page <= 1 ? 'aria-disabled="true"' : ''; ?>>Önceki</a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>" <?php echo $page >= $total_pages ? 'aria-disabled="true"' : ''; ?>>Sonraki</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
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
                            <?php foreach ($categories as $category): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="category/<?php echo $category['id']; ?>" class="text-decoration-none text-dark">
                                        <?php echo $category['name']; ?>
                                    </a>
                                    <?php 
                                    // Count posts in this category
                                    $count_query = "SELECT COUNT(*) as count FROM post_category WHERE category_id = " . $category['id'];
                                    $count_result = mysqli_query($conn, $count_query);
                                    $count_data = mysqli_fetch_assoc($count_result);
                                    ?>
                                    <span class="badge bg-primary rounded-pill"><?php echo $count_data['count']; ?></span>
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
                                    <a href="post/<?php echo $recent['slug']; ?>" class="text-decoration-none">
                                        <div class="row g-0">
                                            <div class="col-3">
                                                <?php if ($recent['featured_image']): ?>
                                                    <img src="assets/images/<?php echo $recent['featured_image']; ?>" class="img-fluid rounded" alt="<?php echo $recent['title']; ?>" style="height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <img src="<?php echo $placeholder_images[array_rand($placeholder_images)]; ?>" class="img-fluid rounded" alt="<?php echo $recent['title']; ?>" style="height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-9">
                                                <p class="mb-0 small fw-bold"><?php echo $recent['title']; ?></p>
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
            
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-tag me-2"></i>Etiketler</h5>
                </div>
                <div class="card-body">
                    <div class="tags">
                        <a href="#" class="badge bg-secondary text-decoration-none me-1 mb-1">PHP</a>
                        <a href="#" class="badge bg-secondary text-decoration-none me-1 mb-1">JavaScript</a>
                        <a href="#" class="badge bg-secondary text-decoration-none me-1 mb-1">CSS</a>
                        <a href="#" class="badge bg-secondary text-decoration-none me-1 mb-1">HTML</a>
                        <a href="#" class="badge bg-secondary text-decoration-none me-1 mb-1">Web</a>
                        <a href="#" class="badge bg-secondary text-decoration-none me-1 mb-1">Developer</a>
                        <a href="#" class="badge bg-secondary text-decoration-none me-1 mb-1">MySQL</a>
                        <a href="#" class="badge bg-secondary text-decoration-none me-1 mb-1">Bootstrap</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?> 