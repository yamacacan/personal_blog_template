<?php
$page_title = "Add Post";
require_once('../includes/functions.php');

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('You need to login as admin to access this page', 'danger');
    redirect('../login.php');
}

// Get all categories
$query = "SELECT * FROM categories ORDER BY name";
$result = mysqli_query($conn, $query);
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = $_POST['content']; // Don't sanitize content as it contains HTML
    $published = isset($_POST['published']) ? 1 : 0;
    $selected_categories = isset($_POST['categories']) ? $_POST['categories'] : [];

    // Validate inputs
    $errors = [];

    if (empty($title)) {
        $errors[] = 'Title is required';
    }

    if (empty($content)) {
        $errors[] = 'Content is required';
    }

    if (empty($errors)) {
        // Generate slug from title
        $slug = generateSlug($title);

        // Check if slug exists
        $slug_check = "SELECT * FROM posts WHERE slug = '$slug'";
        $slug_result = mysqli_query($conn, $slug_check);

        if (mysqli_num_rows($slug_result) > 0) {
            // Append timestamp to make slug unique
            $slug = $slug . '-' . time();
        }

        // Handle image upload if present
        $featured_image = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['size'] > 0) {
            $upload_result = uploadImage($_FILES['featured_image']);

            if ($upload_result['success']) {
                $featured_image = $upload_result['filename'];
            } else {
                $errors[] = $upload_result['message'];
            }
        }

        if (empty($errors)) {
            // Insert post
            $user_id = $_SESSION['user_id'];
            $query = "INSERT INTO posts (title, slug, content, featured_image, published, user_id) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ssssii', $title, $slug, $content, $featured_image, $published, $user_id);

            if (mysqli_stmt_execute($stmt)) {
                $post_id = mysqli_insert_id($conn);

                // Insert categories
                if (!empty($selected_categories)) {
                    foreach ($selected_categories as $category_id) {
                        $cat_query = "INSERT INTO post_category (post_id, category_id) VALUES ($post_id, $category_id)";
                        mysqli_query($conn, $cat_query);
                    }
                }

                setFlashMessage('Post added successfully', 'success');
                redirect('posts.php');
            } else {
                $errors[] = 'Error adding post: ' . mysqli_error($conn);
            }
        }
    }

    if (!empty($errors)) {
        setFlashMessage(implode('<br>', $errors), 'danger');
    }
}

require_once('includes/admin_header.php');
?>

<!-- CKEditor CDN -->
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Add New Post</h1>
    <a href="posts.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Posts
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data" id="postForm">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($_POST['title']) ? $_POST['title'] : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="12"><?php echo isset($_POST['content']) ? $_POST['content'] : ''; ?></textarea>
                        <script>
                            CKEDITOR.replace('content');
                        </script>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title m-0">Publish</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="published" name="published" <?php echo isset($_POST['published']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="published">Publish immediately</label>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Post
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title m-0">Categories</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($categories)): ?>
                                <p>No categories found. <a href="add_category.php">Add a category</a></p>
                            <?php else: ?>
                                <?php foreach ($categories as $category): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>" id="category-<?php echo $category['id']; ?>" <?php echo (isset($_POST['categories']) && in_array($category['id'], $_POST['categories'])) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="category-<?php echo $category['id']; ?>">
                                            <?php echo $category['name']; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title m-0">Featured Image</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                            </div>
                            <div class="text-center">
                                <img id="imagePreview" src="#" alt="Image Preview" class="img-fluid mt-2" style="display: none; max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once('includes/admin_footer.php'); ?>
