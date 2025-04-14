<?php
// No need to require functions.php here as it's already included in the pages
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo isset($page_title) ? $page_title . ' - Blog Admin' : 'Blog Admin'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Admin-specific CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- CKEditor for WYSIWYG editor -->
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper" class="<?php echo isset($_COOKIE['sidebarClosed']) && $_COOKIE['sidebarClosed'] === 'true' ? 'collapsed' : ''; ?>">
            <div class="sidebar-heading">
                <h5 class="mb-0 text-white">Blog Admin</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item bg-transparent text-white">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="posts.php" class="list-group-item bg-transparent text-white">
                    <i class="fas fa-file-alt me-2"></i> Posts
                </a>
                <a href="categories.php" class="list-group-item bg-transparent text-white">
                    <i class="fas fa-folder me-2"></i> Categories
                </a>
                <a href="../index.php" class="list-group-item bg-transparent text-white">
                    <i class="fas fa-home me-2"></i> Visit Site
                </a>
                <a href="../logout.php" class="list-group-item bg-transparent text-white">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="ms-auto d-flex">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> <?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="../profile.php">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid py-3">
                <?php echo flashMessage(); ?> 