<?php
$page_title = "Ana Sayfa";
require_once('includes/functions.php');

// Get latest posts
$latest_posts = getPosts(3);

// Get random featured posts
$query = "SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id 
          WHERE p.published = 1 ORDER BY RAND() LIMIT 3";
$result = mysqli_query($conn, $query);
$featured_projects = [];
while ($row = mysqli_fetch_assoc($result)) {
    $featured_projects[] = $row;
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

<div class="hero">
    <div class="container">
        <div class="hero-content">
            <h2>Hoş Geldiniz</h2>
            <p>Kişisel blog sitemde yazılım, teknoloji ve web geliştirme konularında içerikler paylaşıyorum.</p>
            <div class="cta-buttons">
                <a href="blog" class="btn primary-btn">Blog Yazıları</a>
                <a href="about" class="btn secondary-btn">Hakkımda</a>
            </div>
        </div>
    </div>
</div>

<main class="container">
    <!-- Featured Projects Section -->
    <section class="featured-projects">
        <h2 class="section-title">Öne Çıkan Projeler</h2>
        
        <div class="row">
            <?php if (empty($featured_projects)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Henüz proje bulunamadı.</div>
                </div>
            <?php else: ?>
                <?php foreach ($featured_projects as $index => $project): ?>
                    <div class="col-md-4">
                        <div class="card project-card mb-4">
                            <?php if ($project['featured_image']): ?>
                                <img src="assets/images/<?php echo $project['featured_image']; ?>" class="card-img-top" alt="<?php echo $project['title']; ?>">
                            <?php else: ?>
                                <img src="<?php echo $placeholder_images[array_rand($placeholder_images)]; ?>" class="card-img-top" alt="<?php echo $project['title']; ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h3 class="card-title"><?php echo $project['title']; ?></h3>
                                <p class="card-text"><?php echo substr(strip_tags($project['content']), 0, 120); ?>...</p>
                                <a href="post/<?php echo $project['slug']; ?>" class="btn btn-primary">Detaylar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Latest Posts Section -->
    <section class="latest-posts mt-5">
        <h2 class="section-title">Son Blog Yazıları</h2>
        
        <div class="row">
            <?php if (empty($latest_posts)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Henüz blog yazısı bulunamadı.</div>
                </div>
            <?php else: ?>
                <?php foreach ($latest_posts as $index => $post): ?>
                    <div class="col-md-4">
                        <div class="card blog-card mb-4">
                            <?php if ($post['featured_image']): ?>
                                <img src="assets/images/<?php echo $post['featured_image']; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>">
                            <?php else: ?>
                                <img src="<?php echo $placeholder_images[array_rand($placeholder_images)]; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h3 class="card-title"><?php echo $post['title']; ?></h3>
                                <p class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-calendar-alt"></i> <?php echo date('d F Y', strtotime($post['created_at'])); ?> 
                                    <i class="fas fa-user ms-2"></i> <?php echo $post['username']; ?>
                                </p>
                                
                                <p class="card-text"><?php echo substr(strip_tags($post['content']), 0, 120); ?>...</p>
                                <a href="post/<?php echo $post['slug']; ?>" class="btn btn-outline-primary">Devamını Oku</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="blog" class="btn btn-lg btn-primary">Tüm Yazıları Gör</a>
        </div>
    </section>
    
    <!-- About Section -->
    <section class="about-section mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="<?php echo $placeholder_images[0]; ?>" class="img-fluid rounded" alt="Ahmet Can Yamaç">
                    </div>
                    <div class="col-md-8">
                        <h2 class="card-title">Hakkımda</h2>
                        <p class="card-text">Merhaba, ben Ahmet Can Yamaç. Web geliştirme ve yazılım teknolojileri konusunda deneyimli bir geliştiriciyim. Bu blogda, öğrendiklerimi paylaşıyor ve teknoloji dünyasındaki gelişmeleri takip ediyorum.</p>
                        <div class="accordion" id="aboutAccordion">
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        Becerilerim
                                    </button>
                                </h3>
                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#aboutAccordion">
                                    <div class="accordion-body">
                                        <ul>
                                            <li>Web Geliştirme (HTML, CSS, JavaScript)</li>
                                            <li>PHP ve MySQL</li>
                                            <li>Front-end Frameworks (Bootstrap, React)</li>
                                            <li>Back-end Development</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Eğitim
                                    </button>
                                </h3>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#aboutAccordion">
                                    <div class="accordion-body">
                                        <p>Bilgisayar Mühendisliği lisans derecesi sahibiyim ve sürekli olarak kendimi geliştirmek için online kurslara katılıyorum.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="about" class="btn btn-outline-primary mt-3">Daha Fazla Bilgi</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once('includes/footer.php'); ?> 