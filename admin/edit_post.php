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

// Fetch post using the getPost function
$post = getPost($post_id, false, true);

if (!$post) {
    setFlashMessage('Post not found', 'danger');
    redirect('posts.php');
}

// Get all categories
$categories = getCategories();

// Get selected categories
$selected_categories = [];
$post_categories = getPostCategories($post_id);
foreach ($post_categories as $category) {
    $selected_categories[] = $category['id'];
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
                // Update categories using prepared statements
                $delete_query = "DELETE FROM post_category WHERE post_id = ?";
                $delete_stmt = mysqli_prepare($conn, $delete_query);
                mysqli_stmt_bind_param($delete_stmt, "i", $post_id);
                mysqli_stmt_execute($delete_stmt);
                mysqli_stmt_close($delete_stmt);
                
                if (!empty($new_categories)) {
                    $insert_query = "INSERT INTO post_category (post_id, category_id) VALUES (?, ?)";
                    $insert_stmt = mysqli_prepare($conn, $insert_query);
                    
                    foreach ($new_categories as $category_id) {
                        mysqli_stmt_bind_param($insert_stmt, "ii", $post_id, $category_id);
                        mysqli_stmt_execute($insert_stmt);
                    }
                    
                    mysqli_stmt_close($insert_stmt);
                }
                
                setFlashMessage('Post updated successfully', 'success');
                redirect('posts.php');
            } else {
                $errors[] = 'Error updating post: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (!empty($errors)) {
        setFlashMessage(implode('<br>', $errors), 'danger');
    }
}

require_once('includes/admin_header.php');
?>

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Post</h1>
    <a href="posts.php" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to Posts
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-4">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="ckeditor" id="content" name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h6 class="card-title m-0 font-weight-bold">Publish</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="published" name="published" <?php echo $post['published'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="published">Publish immediately</label>
                            </div>
                            <p class="small text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i> Last updated: <?php echo date('M d, Y H:i', strtotime($post['updated_at'])); ?>
                            </p>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Post
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
                                    <p class="text-muted">No categories found</p>
                                <?php else: ?>
                                    <?php foreach ($categories as $category): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>" id="category-<?php echo $category['id']; ?>" <?php echo in_array($category['id'], $selected_categories) ? 'checked' : ''; ?>>
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
                            <?php if ($post['featured_image']): ?>
                                <div class="text-center">
                                    <img src="../uploads/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="Featured Image" class="img-fluid mt-2 img-thumbnail" style="max-height: 200px;">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
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