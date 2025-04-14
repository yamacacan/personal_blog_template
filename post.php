<?php
require_once('includes/functions.php');

// Get post by slug
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    redirect('blog.php');
}

$post = getPost($slug, true);

if (!$post || $post['published'] != 1) {
    setFlashMessage('Yazı bulunamadı', 'danger');
    redirect('blog.php');
}

$page_title = $post['title'];
require_once('includes/header.php');

$post_categories = getPostCategories($post['id']);
?>

<div class="container my-5">
    <div class="row g-5">
        <!-- Ana İçerik -->
        <div class="col-lg-8">
            <article class="p-4 rounded shadow-sm bg-white">
            <section class="mb-4 border-bottom pb-3">
                    <h1 class="display-5 fw-bold"><?php echo $post['title']; ?></h1>
                    <div class="d-flex flex-wrap align-items-center text-muted small mt-2">
                        <div class="me-3">
                            <i class="fas fa-user me-1"></i> <?php echo $post['username']; ?>
                        </div>
                        <div class="me-3">
                            <i class="fas fa-calendar me-1"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                        </div>
                        <?php if (!empty($post_categories)): ?>
                            <div>
                                <i class="fas fa-tags me-1"></i>
                                <?php
                                $category_links = [];
                                foreach ($post_categories as $cat) {
                                    $category_links[] = '<a href="category.php?slug=' . $cat['slug'] . '" class="badge bg-light text-dark me-1">' . $cat['name'] . '</a>';
                                }
                                echo implode(' ', $category_links);
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
            </section>

                <?php if ($post['featured_image']): ?>
                    <img src="assets/images/<?php echo $post['featured_image']; ?>" alt="<?php echo $post['title']; ?>" class="img-fluid rounded mb-4 shadow-sm">
                <?php endif; ?>

                <div class="blog-content lh-lg">
                    <?php echo $post['content']; ?>
                </div>
            </article>

            <div class="my-4 p-3 bg-white rounded shadow-sm">
                <h5>Bu Yazıyı Paylaş</h5>
                <div class="d-flex mt-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-primary me-2">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="btn btn-info text-white me-2">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($post['title']); ?>" target="_blank" class="btn btn-secondary">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Kenar Çubuğu -->
        <div class="col-lg-4">
            <div class="p-4 mb-4 bg-white rounded shadow-sm">
                <h5 class="mb-3">Ara</h5>
                <form action="search.php" method="GET" class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Arama..." required>
                    <button class="btn btn-dark" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="p-4 mb-4 bg-white rounded shadow-sm">
                <h5 class="mb-3">Kategoriler</h5>
                <ul class="list-group list-group-flush">
                    <?php
                    $all_categories = getCategories();
                    if (empty($all_categories)): ?>
                        <li class="list-group-item">Kategori bulunamadı</li>
                    <?php else: ?>
                        <?php foreach ($all_categories as $category): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="category.php?slug=<?php echo $category['slug']; ?>" class="text-decoration-none">
                                    <?php echo $category['name']; ?>
                                </a>
                                <?php
                                $cat_id = $category['id'];
                                $count_query = "SELECT COUNT(*) as count FROM post_category pc 
                                                JOIN posts p ON pc.post_id = p.id 
                                                WHERE pc.category_id = $cat_id AND p.published = 1";
                                $count_result = mysqli_query($conn, $count_query);
                                $count_data = mysqli_fetch_assoc($count_result);
                                ?>
                                <span class="badge bg-dark"><?php echo $count_data['count']; ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="p-4 mb-4 bg-white rounded shadow-sm">
                <h5 class="mb-3">Son Yazılar</h5>
                <ul class="list-unstyled">
                    <?php
                    $recent_posts = getPosts(5);
                    if (empty($recent_posts)): ?>
                        <li>Henüz yazı yok</li>
                    <?php else: ?>
                        <?php foreach ($recent_posts as $recent): ?>
                            <li class="mb-3">
                                <a href="post.php?slug=<?php echo $recent['slug']; ?>" class="fw-semibold text-decoration-none d-block">
                                    <?php echo $recent['title']; ?>
                                </a>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i> <?php echo date('M d, Y', strtotime($recent['created_at'])); ?>
                                </small>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
