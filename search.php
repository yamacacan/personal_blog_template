<?php
$page_title = "Arama Sonuçları";
require_once('includes/functions.php');

// Search functionality
$search_query = '';
$posts = [];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_query = sanitize($_GET['q']);
    
    // Search in post title and content
    $query = "SELECT p.*, u.username 
              FROM posts p
              JOIN users u ON p.user_id = u.id
              WHERE (p.title LIKE '%$search_query%' OR p.content LIKE '%$search_query%') 
              AND p.published = 1
              ORDER BY p.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }
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

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Arama Sonuçları: "<?php echo htmlspecialchars($search_query); ?>"</h2>
            
            <?php if (empty($search_query)): ?>
                <div class="alert alert-info">
                    <p>Lütfen arama kutusuna bir anahtar kelime girin.</p>
                </div>
            <?php elseif (empty($posts)): ?>
                <div class="alert alert-warning">
                    <p>"<?php echo htmlspecialchars($search_query); ?>" için hiçbir sonuç bulunamadı.</p>
                    <p>Öneriler:</p>
                    <ul>
                        <li>Farklı anahtar kelimeler deneyin</li>
                        <li>Daha genel terimler kullanın</li>
                        <li>Yazım hatası olmadığından emin olun</li>
                    </ul>
                </div>
            <?php else: ?>
                <p class="mb-4"><?php echo count($posts); ?> sonuç bulundu.</p>
                
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <?php if ($post['featured_image']): ?>
                                    <img src="assets/images/<?php echo $post['featured_image']; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>">
                                <?php else: ?>
                                    <img src="<?php echo $placeholder_images[array_rand($placeholder_images)]; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h3 class="card-title h5"><?php echo $post['title']; ?></h3>
                                    <p class="card-subtitle mb-2 text-muted small">
                                        <i class="fas fa-calendar-alt"></i> <?php echo date('d F Y', strtotime($post['created_at'])); ?> 
                                        <i class="fas fa-user ms-2"></i> <?php echo $post['username']; ?>
                                    </p>
                                    
                                    <?php 
                                    // Highlight the search term in content
                                    $content = strip_tags($post['content']);
                                    $pos = stripos($content, $search_query);
                                    if ($pos !== false) {
                                        // Get a portion of text around the search term
                                        $start = max(0, $pos - 50);
                                        $length = 150;
                                        if ($start > 0) {
                                            $excerpt = '...' . substr($content, $start, $length) . '...';
                                        } else {
                                            $excerpt = substr($content, 0, $length) . '...';
                                        }
                                        
                                        // Highlight the search term
                                        $excerpt = preg_replace('/(' . preg_quote($search_query, '/') . ')/i', '<mark>$1</mark>', $excerpt);
                                    } else {
                                        $excerpt = substr($content, 0, 150) . '...';
                                    }
                                    ?>
                                    
                                    <p class="card-text"><?php echo $excerpt; ?></p>
                                    <a href="post/<?php echo $post['slug']; ?>" class="btn btn-outline-primary">Devamını Oku</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="blog" class="btn btn-primary">Tüm Blog Yazıları</a>
                <a href="index" class="btn btn-outline-secondary ms-2">Ana Sayfa</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?> 