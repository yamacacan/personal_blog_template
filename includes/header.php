<?php
require_once('includes/functions.php');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Ahmet Can Yamaç' : 'Ahmet Can Yamaç - Portfolio & Blog'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>index">
                    <h1 class="h3 mb-0">Ahmet Can Yamaç</h1>
                    <p class="tagline small mb-0">Web Developer & Technical Writer</p>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <?php
                        $current_page = basename($_SERVER['PHP_SELF'], '.php');
                        ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>" href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>index">Ana Sayfa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'about' ? 'active' : ''; ?>" href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>about">Hakkımda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'blog' ? 'active' : ''; ?>" href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>blog">Blog</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'contact' ? 'active' : ''; ?>" href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>contact">İletişim</a>
                        </li>
                        <?php if (isLoggedIn() && isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo strpos($current_page, 'admin') !== false ? 'active' : ''; ?>" href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>admin">Admin</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <form class="d-flex" action="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>search.php" method="GET">
                        <div class="input-group">
                            <input class="form-control" type="search" name="q" placeholder="Blog'da ara..." aria-label="Search" required>
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    
    <?php echo flashMessage(); ?> 