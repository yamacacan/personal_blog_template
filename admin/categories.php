<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$page_title = "Kategori Yönetimi";
$page_depth = 1;
require_once('../includes/functions.php');
require_once('includes/admin_header.php');

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('Bu sayfaya erişim izniniz yok.', 'danger');
    redirect('../index.php');
}

// Handle category operations
if (isset($_POST['submit'])) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    
    // Create new category
    if (empty($_POST['category_id'])) {
        if (empty($name)) {
            setFlashMessage('Kategori adı gereklidir.', 'danger');
        } else {
            $query = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
            if (mysqli_query($conn, $query)) {
                setFlashMessage('Kategori başarıyla oluşturuldu.', 'success');
                redirect('categories.php');
            } else {
                setFlashMessage('Kategori oluşturulurken bir hata oluştu: ' . mysqli_error($conn), 'danger');
            }
        }
    } 
    // Update existing category
    else {
        $category_id = (int)$_POST['category_id'];
        if (empty($name)) {
            setFlashMessage('Kategori adı gereklidir.', 'danger');
        } else {
            $query = "UPDATE categories SET name = '$name', description = '$description' WHERE id = $category_id";
            if (mysqli_query($conn, $query)) {
                setFlashMessage('Kategori başarıyla güncellendi.', 'success');
                redirect('categories.php');
            } else {
                setFlashMessage('Kategori güncellenirken bir hata oluştu: ' . mysqli_error($conn), 'danger');
            }
        }
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    
    // Check if there are posts in this category
    $check_query = "SELECT COUNT(*) as count FROM post_category WHERE category_id = $category_id";
    $check_result = mysqli_query($conn, $check_query);
    $check_data = mysqli_fetch_assoc($check_result);
    
    if ($check_data['count'] > 0) {
        setFlashMessage('Bu kategoriye ait yazılar bulunmaktadır. İlk önce onları kaldırın veya başka kategoriye taşıyın.', 'warning');
    } else {
        $query = "DELETE FROM categories WHERE id = $category_id";
        if (mysqli_query($conn, $query)) {
            setFlashMessage('Kategori başarıyla silindi.', 'success');
        } else {
            setFlashMessage('Kategori silinirken bir hata oluştu: ' . mysqli_error($conn), 'danger');
        }
    }
    redirect('categories.php');
}

// Edit category - get category data
$category = null;
if (isset($_GET['edit'])) {
    $category_id = (int)$_GET['edit'];
    $query = "SELECT * FROM categories WHERE id = $category_id";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $category = mysqli_fetch_assoc($result);
    }
}

// Get all categories
$query = "SELECT * FROM categories ORDER BY name ASC";
$result = mysqli_query($conn, $query);


?>

<div class="container-fluid">
    <div class="row">
        <?php include('includes/admin_sidebar.php'); ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Kategori Yönetimi</h1>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <?php echo isset($category) ? 'Kategori Düzenle' : 'Yeni Kategori Ekle'; ?>
                        </div>
                        <div class="card-body">
                            <form action="categories.php" method="post">
                                <?php if (isset($category)): ?>
                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Kategori Adı</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($category) ? $category['name'] : ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Açıklama</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($category) ? $category['description'] : ''; ?></textarea>
                                </div>
                                
                                <button type="submit" name="submit" class="btn btn-primary"><?php echo isset($category) ? 'Güncelle' : 'Ekle'; ?></button>
                                
                                <?php if (isset($category)): ?>
                                    <a href="categories.php" class="btn btn-secondary">İptal</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            Kategoriler
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ad</th>
                                            <th>Açıklama</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($result) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td><?php echo $row['id']; ?></td>
                                                    <td><?php echo $row['name']; ?></td>
                                                    <td><?php echo $row['description']; ?></td>
                                                    <td>
                                                        <a href="categories.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                                                        <a href="categories.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')">Sil</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Henüz kategori eklenmemiş.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once('includes/admin_footer.php'); ?> 