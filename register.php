<?php
$page_title = "Kayıt";
require_once('includes/functions.php');

// Admin hesabı oluşturulmuşsa yönlendir
$query = "SELECT * FROM users WHERE is_admin = 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    // Admin hesabı zaten var, login sayfasına yönlendir
    setFlashMessage('Kayıt işlemi devre dışı bırakılmıştır. Lütfen yönetici ile iletişime geçin.', 'warning');
    redirect('login');
}

// Admin yoksa ilk admin kaydını yap
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_key = sanitize($_POST['admin_key']);
    
    $errors = [];
    
    // Özel yönetici anahtarını kontrol et
    if ($admin_key !== "CENG311_ADMIN_KEY") {
        $errors[] = 'Geçersiz yönetici anahtarı';
    }
    
    // Diğer doğrulamaları yap
    if (empty($username)) {
        $errors[] = 'Kullanıcı adı zorunludur';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Kullanıcı adı en az 3 karakter olmalıdır';
    }
    
    if (empty($email)) {
        $errors[] = 'E-posta zorunludur';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir e-posta giriniz';
    }
    
    if (empty($password)) {
        $errors[] = 'Şifre zorunludur';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Şifre en az 6 karakter olmalıdır';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Şifreler eşleşmiyor';
    }
    
    if (empty($errors)) {
        // Şifreyi hashle
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Admin kullanıcısını ekle
        $query = "INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 1)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $hashed_password);
        
        if (mysqli_stmt_execute($stmt)) {
            setFlashMessage('Admin kaydı başarılı! Şimdi giriş yapabilirsiniz.', 'success');
            redirect('login');
        } else {
            setFlashMessage('Hata: ' . mysqli_error($conn), 'danger');
        }
    } else {
        // Hataları göster
        setFlashMessage(implode('<br>', $errors), 'danger');
    }
}

require_once('includes/header.php');
?>

<div class="container">
    <div class="auth-form mt-5">
        <h2 class="form-title">İlk Admin Kaydı</h2>
        <p>Bu form yalnızca ilk yönetici hesabını oluşturmak içindir. Kaydedildikten sonra bu sayfa devre dışı bırakılacaktır.</p>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($username) ? $username : ''; ?>" required>
                <div class="form-text">Kullanıcı adı en az 3 karakter olmalıdır</div>
            </div>
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="form-text">Şifre en az 6 karakter olmalıdır</div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Şifre Tekrar</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="admin_key">Yönetici Anahtarı</label>
                <input type="password" class="form-control" id="admin_key" name="admin_key" required>
                <div class="form-text">Yönetici anahtarı: CENG311_ADMIN_KEY</div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Yönetici Hesabı Oluştur</button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            <p>Hesabınız var mı? <a href="login">Giriş Yap</a></p>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?> 