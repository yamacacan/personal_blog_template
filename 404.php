<?php
$page_title = "Sayfa Bulunamadı";
require_once('includes/functions.php');
require_once('includes/header.php');
?>

<div class="page-header">
    <div class="container">
        <h2>404 - Sayfa Bulunamadı</h2>
    </div>
</div>

<main class="container">
    <section class="error-container">
        <div class="error-content">
            <h3>Oops! Aradığınız sayfayı bulamadık.</h3>
            <p>Aradığınız sayfa kaldırılmış, adı değiştirilmiş veya geçici olarak kullanılamıyor olabilir.</p>
            <div class="error-actions">
                <a href="index" class="btn primary-btn">Ana Sayfaya Dön</a>
                <a href="blog" class="btn secondary-btn">Blog Yazılarına Göz At</a>
            </div>
        </div>
    </section>
</main>

<?php require_once('includes/footer.php'); ?> 