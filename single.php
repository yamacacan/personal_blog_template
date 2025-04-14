<?php
require_once('includes/functions.php');

// Get post by slug
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    redirect('blog');
}

$post = getPost($slug, true);

if (!$post || $post['published'] != 1) {
    setFlashMessage('Yazı bulunamadı', 'danger');
    redirect('blog');
}

$page_title = $post['title'];
$page_depth = 0;
require_once('includes/header.php');

// Get post categories
$post_categories = getPostCategories($post['id']);

// Get post comments
$comments = getComments($post['id']);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $comment_text = sanitize($_POST['comment']);
    
    if (empty($name) || empty($email) || empty($comment_text)) {
        setFlashMessage('Tüm alanlar zorunludur', 'danger');
    } else {
        $query = "INSERT INTO comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'isss', $post['id'], $name, $email, $comment_text);
        
        if (mysqli_stmt_execute($stmt)) {
            setFlashMessage('Yorumunuz başarıyla gönderildi ve onay bekliyor', 'success');
        } else {
            setFlashMessage('Yorum gönderilirken hata oluştu', 'danger');
        }
        
        // Redirect to avoid form resubmission
        redirect("single?slug=$slug");
    }
}
?>

<div class="page-header">
    <div class="container">
        <h2><?php echo $post['title']; ?></h2>
    </div>
</div>

<main class="container">
    <article class="blog-post-single">
        <!-- Post Meta -->
        <div class="blog-post-meta">
            <span class="author"><i class="fas fa-user"></i> <?php echo $post['username']; ?></span>
            <span class="date"><i class="fas fa-calendar"></i> <?php echo date('d F Y', strtotime($post['created_at'])); ?></span>
            
            <?php if (!empty($post_categories)): ?>
                <span class="categories">
                <i class="fas fa-tags"></i> 
                <?php 
                $category_links = [];
                foreach ($post_categories as $cat) {
                    $category_links[] = '<a href="category.php?slug=' . $cat['slug'] . '">' . $cat['name'] . '</a>';
                }
                echo implode(', ', $category_links);
                ?>
                </span>
            <?php endif; ?>
        </div>
        
        <!-- Featured Image -->
        <?php if ($post['featured_image']): ?>
            <div class="blog-featured-image">
                <img src="assets/images/<?php echo $post['featured_image']; ?>" alt="<?php echo $post['title']; ?>">
            </div>
        <?php endif; ?>
        
        <!-- Post Content -->
        <div class="blog-content">
            <?php echo $post['content']; ?>
        </div>
        
        <!-- Social Share -->
        <div class="blog-share">
            <h3>Bu Yazıyı Paylaş</h3>
            <div class="social-icons">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="social-icon">Facebook</a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="social-icon">Twitter</a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($post['title']); ?>" target="_blank" class="social-icon">LinkedIn</a>
            </div>
        </div>
    </article>
    
    <!-- Comments Section -->
    <section class="comments-section">
        <h3><?php echo count($comments); ?> Yorum</h3>
        
        <?php if (!empty($comments)): ?>
            <div class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-avatar">
                            <img src="https://via.placeholder.com/50" alt="<?php echo $comment['name']; ?>">
                        </div>
                        <div class="comment-content">
                            <h4><?php echo $comment['name']; ?></h4>
                            <div class="comment-meta"><?php echo date('d F Y - H:i', strtotime($comment['created_at'])); ?></div>
                            <p><?php echo nl2br($comment['comment']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Henüz yorum yapılmamış. İlk yorumu siz yapın!</p>
        <?php endif; ?>
        
        <!-- Comment Form -->
        <div class="comment-form-container">
            <h3>Yorum Yapın</h3>
            <form method="POST" action="" class="comment-form" id="commentForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">İsim *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-posta *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="comment">Yorum *</label>
                    <textarea id="comment" name="comment" rows="5" required></textarea>
                </div>
                <button type="submit" name="submit_comment" class="btn">Yorum Gönder</button>
            </form>
        </div>
    </section>
</main>

<?php require_once('includes/footer.php'); ?> 