<?php
$page_title = "Add Post";
require_once('../includes/functions.php');

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('You need to login as admin to access this page', 'danger');
    redirect('../login.php');
}

// Get all categories
$categories = getCategories();

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
        global $conn;
        $slug_check = "SELECT id FROM posts WHERE slug = ?";
        $stmt = mysqli_prepare($conn, $slug_check);
        mysqli_stmt_bind_param($stmt, "s", $slug);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            // Append timestamp to make slug unique
            $slug = $slug . '-' . time();
        }
        mysqli_stmt_close($stmt);

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
                mysqli_stmt_close($stmt);

                // Insert categories
                if (!empty($selected_categories)) {
                    $cat_query = "INSERT INTO post_category (post_id, category_id) VALUES (?, ?)";
                    $cat_stmt = mysqli_prepare($conn, $cat_query);
                    
                    foreach ($selected_categories as $category_id) {
                        mysqli_stmt_bind_param($cat_stmt, "ii", $post_id, $category_id);
                        mysqli_stmt_execute($cat_stmt);
                    }
                    mysqli_stmt_close($cat_stmt);
                }

                setFlashMessage('Post added successfully', 'success');
                redirect('posts.php');
            } else {
                $errors[] = 'Error adding post: ' . mysqli_error($conn);
                mysqli_stmt_close($stmt);
            }
        }
    }

    if (!empty($errors)) {
        setFlashMessage(implode('<br>', $errors), 'danger');
    }
}

require_once('includes/admin_header.php');
?>

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add New Post</h1>
    <a href="posts.php" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to Posts
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data" id="postForm">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-4">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="ckeditor" id="content" name="content"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h6 class="card-title m-0 font-weight-bold">Publish</h6>
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

                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h6 class="card-title m-0 font-weight-bold">Categories</h6>
                        </div>
                        <div class="card-body">
                            <div class="category-list" style="max-height: 200px; overflow-y: auto;">
                                <?php if (empty($categories)): ?>
                                    <p class="text-muted">No categories found. <a href="categories.php" class="text-primary">Manage categories</a></p>
                                <?php else: ?>
                                    <?php foreach ($categories as $category): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>" id="category-<?php echo $category['id']; ?>" <?php echo (isset($_POST['categories']) && in_array($category['id'], $_POST['categories'])) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="category-<?php echo $category['id']; ?>">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header py-3">
                            <h6 class="card-title m-0 font-weight-bold">Featured Image</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                                <small class="text-muted">Recommended size: 1200x630 pixels</small>
                            </div>
                            <div class="text-center">
                                <img id="imagePreview" src="#" alt="Image Preview" class="img-fluid mt-2 img-thumbnail" style="display: none; max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    document.getElementById('featured_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
    
    // Initialize CKEditor
    CKEDITOR.replace('content', {
        height: 400,
        toolbarGroups: [
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
            { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
            { name: 'forms', groups: [ 'forms' ] },
            '/',
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
            { name: 'links', groups: [ 'links' ] },
            { name: 'insert', groups: [ 'insert' ] },
            '/',
            { name: 'styles', groups: [ 'styles' ] },
            { name: 'colors', groups: [ 'colors' ] },
            { name: 'tools', groups: [ 'tools' ] },
            { name: 'others', groups: [ 'others' ] },
            { name: 'about', groups: [ 'about' ] }
        ],
        removeButtons: 'Save,NewPage,ExportPdf,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CopyFormatting,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,Font,ShowBlocks,About',
        filebrowserUploadUrl: 'upload.php',
        filebrowserImageUploadUrl: 'upload.php?type=Images'
    });
});
</script>

<?php require_once('includes/admin_footer.php'); ?>
