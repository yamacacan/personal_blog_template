<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$page_title = "Manage Categories";
$page_depth = 1;
require_once('../includes/functions.php');
require_once('includes/admin_header.php');

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('You need to login as admin to access this page', 'danger');
    redirect('../login.php');
}

// Handle category operations
if (isset($_POST['submit'])) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    
    global $conn;
    // Create new category
    if (empty($_POST['category_id'])) {
        if (empty($name)) {
            setFlashMessage('Category name is required', 'danger');
        } else {
            // Generate slug from category name
            $slug = generateSlug($name);
            
            // Check if slug exists
            $slug_check = "SELECT id FROM categories WHERE slug = ?";
            $check_stmt = mysqli_prepare($conn, $slug_check);
            mysqli_stmt_bind_param($check_stmt, "s", $slug);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                // Append timestamp to make slug unique
                $slug = $slug . '-' . time();
            }
            mysqli_stmt_close($check_stmt);
            
            $query = "INSERT INTO categories (name, description, slug) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sss", $name, $description, $slug);
            
            if (mysqli_stmt_execute($stmt)) {
                setFlashMessage('Category added successfully', 'success');
                redirect('categories.php');
            } else {
                setFlashMessage('Error adding category: ' . mysqli_error($conn), 'danger');
            }
            mysqli_stmt_close($stmt);
        }
    } 
    // Update existing category
    else {
        $category_id = (int)$_POST['category_id'];
        if (empty($name)) {
            setFlashMessage('Category name is required', 'danger');
        } else {
            // Generate slug if needed
            $slug = generateSlug($name);
            
            // Check if slug exists for other categories
            $slug_check = "SELECT id FROM categories WHERE slug = ? AND id != ?";
            $check_stmt = mysqli_prepare($conn, $slug_check);
            mysqli_stmt_bind_param($check_stmt, "si", $slug, $category_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                // Append timestamp to make slug unique
                $slug = $slug . '-' . time();
            }
            mysqli_stmt_close($check_stmt);
            
            $query = "UPDATE categories SET name = ?, description = ?, slug = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $name, $description, $slug, $category_id);
            
            if (mysqli_stmt_execute($stmt)) {
                setFlashMessage('Category updated successfully', 'success');
                redirect('categories.php');
            } else {
                setFlashMessage('Error updating category: ' . mysqli_error($conn), 'danger');
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    
    global $conn;
    // Check if there are posts in this category
    $check_query = "SELECT COUNT(*) as count FROM post_category WHERE category_id = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    $check_data = mysqli_fetch_assoc($check_result);
    mysqli_stmt_close($stmt);
    
    if ($check_data['count'] > 0) {
        setFlashMessage('This category contains posts. Please remove them or reassign to another category first.', 'warning');
    } else {
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $category_id);
        
        if (mysqli_stmt_execute($stmt)) {
            setFlashMessage('Category deleted successfully', 'success');
        } else {
            setFlashMessage('Error deleting category: ' . mysqli_error($conn), 'danger');
        }
        mysqli_stmt_close($stmt);
    }
    redirect('categories.php');
}

// Edit category - get category data
$category = null;
if (isset($_GET['edit'])) {
    $category_id = (int)$_GET['edit'];
    
    global $conn;
    $query = "SELECT * FROM categories WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $category = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

// Get all categories
$categories = getCategories();

?>

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage Categories</h1>
    <a href="posts.php" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to Posts
    </a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="card-title m-0 font-weight-bold">
                    <?php echo isset($category) ? 'Edit Category' : 'Add New Category'; ?>
                </h6>
            </div>
            <div class="card-body">
                <form action="categories.php" method="post">
                    <?php if (isset($category)): ?>
                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($category) ? htmlspecialchars($category['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($category) ? htmlspecialchars($category['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="submit" class="btn btn-primary">
                            <i class="fas fa-<?php echo isset($category) ? 'save' : 'plus'; ?> me-1"></i>
                            <?php echo isset($category) ? 'Update Category' : 'Add Category'; ?>
                        </button>
                        
                        <?php if (isset($category)): ?>
                            <a href="categories.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title m-0 font-weight-bold">Categories</h6>
                <span class="badge bg-primary"><?php echo count($categories); ?> Categories</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No categories found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><?php echo $cat['id']; ?></td>
                                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                        <td><?php echo htmlspecialchars($cat['description']); ?></td>
                                        <td class="table-action-buttons">
                                            <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="categories.php?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/admin_footer.php'); ?> 