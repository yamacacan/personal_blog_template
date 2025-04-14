<?php
$page_title = "Giriş";
require_once('includes/functions.php');

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index');
}

// Process login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        setFlashMessage('Kullanıcı adı ve şifre zorunludur', 'danger');
    } else {
        $user = validateLogin($username, $password);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Redirect to appropriate page
            if ($user['is_admin']) {
                redirect('admin');
            } else {
                redirect('index');
            }
        } else {
            setFlashMessage('Geçersiz kullanıcı adı veya şifre', 'danger');
        }
    }
}

require_once('includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i>Kullanıcı Girişi</h4>
                </div>
                <div class="card-body p-4">
                    <?php echo flashMessage(); ?>
                    
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-1 text-muted"></i> Kullanıcı Adı
                            </label>
                            <input type="text" class="form-control" id="username" name="username" required 
                                   placeholder="Kullanıcı adınızı girin" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1 text-muted"></i> Şifre
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required
                                   placeholder="Şifrenizi girin">
                        </div>
                        
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                            </button>
                        </div>
                    </form>
                    
                    <?php
                    // Admin hesabı kontrol et
                    $query = "SELECT * FROM users WHERE is_admin = 1";
                    $result = mysqli_query($conn, $query);
                    
                    if (mysqli_num_rows($result) == 0):
                    ?>
                    <div class="text-center mt-3 border-top pt-3">
                        <p class="mb-0">Admin hesabı oluşturulmamış.</p>
                        <a href="register" class="btn btn-outline-primary btn-sm mt-2">
                            <i class="fas fa-user-plus me-1"></i>Admin Kaydı Oluştur
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?> 