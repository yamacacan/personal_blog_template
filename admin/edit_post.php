<?php
$page_title = "Edit Post";
require_once('../includes/functions.php');

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('You need to login as admin to access this page', 'danger');
    redirect('../login.php');
}

// Get post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlashMessage('Invalid post ID', 'danger');
    redirect('posts.php');
}

$post_id = (int)$_GET['id'];

// Fetch post
$post_query = "SELECT * FROM posts WHERE id = $post_id";
$post_result = mysqli_query($conn, $post_query);
if (mysqli_num_rows($post_result) == 0) {
    setFlashMessage('Post not found', 'danger');
    redirect('posts.php');
}

$post = mysqli_fetch_assoc($post_result);

// Get all categories
$cat_query = "SELECT * FROM categories ORDER BY name";
$cat_result = mysqli_query($conn, $cat_query);
$categories = [];
while ($row = mysqli_fetch_assoc($cat_result)) {
    $categories[] = $row;
}

// Get selected categories
$selected_cats_query = "SELECT category_id FROM post_category WHERE post_id = $post_id";
$selected_cats_result = mysqli_query($conn, $selected_cats_query);
$selected_categories = [];
while ($row = mysqli_fetch_assoc($selected_cats_result)) {
    $selected_categories[] = $row['category_id'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = $_POST['content'];
    $published = isset($_POST['published']) ? 1 : 0;
    $new_categories = isset($_POST['categories']) ? $_POST['categories'] : [];

    $errors = [];
    if (empty($title)) $errors[] = 'Title is required';
    if (empty($content)) $errors[] = 'Content is required';

    if (empty($errors)) {
        $featured_image = $post['featured_image'];
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['size'] > 0) {
            $upload_result = uploadImage($_FILES['featured_image']);
            if ($upload_result['success']) {
                $featured_image = $upload_result['filename'];
            } else {
                $errors[] = $upload_result['message'];
            }
        }

        if (empty($errors)) {
            $query = "UPDATE posts SET title = ?, content = ?, featured_image = ?, published = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'sssii', $title, $content, $featured_image, $published, $post_id);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_query($conn, "DELETE FROM post_category WHERE post_id = $post_id");
                foreach ($new_categories as $category_id) {
                    mysqli_query($conn, "INSERT INTO post_category (post_id, category_id) VALUES ($post_id, $category_id)");
                }
                setFlashMessage('Post updated successfully', 'success');
                redirect('posts.php');
            } else {
                $errors[] = 'Error updating post: ' . mysqli_error($conn);
            }
        }
    }

    if (!empty($errors)) {
        setFlashMessage(implode('<br>', $errors), 'danger');
    }
}

require_once('includes/admin_header.php');
?>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Edit Post</h1>
    <a href="posts.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Posts
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="12"><?php echo htmlspecialchars($post['content']); ?></textarea>
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
                                <input type="checkbox" class="form-check-input" id="published" name="published" <?php echo $post['published'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="published">Publish immediately</label>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Post
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title m-0">Categories</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($categories as $category): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>" id="category-<?php echo $category['id']; ?>" <?php echo in_array($category['id'], $selected_categories) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="category-<?php echo $category['id']; ?>">
                                        <?php echo $category['name']; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
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
                            <?php if ($post['featured_image']): ?>
                                <div class="text-center">
                                    <img src="../uploads/<?php echo $post['featured_image']; ?>" alt="Featured Image" class="img-fluid mt-2" style="max-height: 200px;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once('includes/admin_footer.php'); ?>