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

<div class="page-header">
    <div class="container">
        <h2>Giriş</h2>
    </div>
</div>

<div class="container">
    <div class="auth-form mt-5">
        <h2 class="form-title">Giriş</h2>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Giriş Yap</button>
            </div>
        </form>
        
        <?php
        // Admin hesabı kontrol et
        $query = "SELECT * FROM users WHERE is_admin = 1";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 0):
        ?>
        <div class="text-center mt-3">
            <p>Admin hesabı oluşturulmamış. <a href="register">Admin Kaydı</a></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once('includes/footer.php'); ?> 