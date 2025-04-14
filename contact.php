<?php
$page_title = "İletişim";
require_once('includes/functions.php');

// İletişim formu işleme
$message_sent = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_message'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    // Basit doğrulama
    if (empty($name)) {
        $errors[] = 'İsim alanı zorunludur';
    }
    
    if (empty($email)) {
        $errors[] = 'E-posta alanı zorunludur';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir e-posta adresi giriniz';
    }
    
    if (empty($message)) {
        $errors[] = 'Mesaj alanı zorunludur';
    }
    
    if (empty($errors)) {
        // Mesajı veritabanına kaydet
        $query = "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $subject, $message);
            
            if (mysqli_stmt_execute($stmt)) {
                $message_sent = true;
                
                // E-posta gönderme kodu burada olabilir
                // mail('admin@example.com', 'Yeni İletişim Formu Mesajı', $message, 'From: ' . $email);
                
                // Form alanlarını temizle
                $name = $email = $subject = $message = '';
            } else {
                $errors[] = 'Mesaj gönderirken bir hata oluştu: ' . mysqli_error($conn);
            }
        } else {
            $errors[] = 'Veritabanı hatası: ' . mysqli_error($conn);
        }
    }
}

require_once('includes/header.php');
?>

<div class="page-header">
    <div class="container">
        <h2>İletişim</h2>
    </div>
</div>

<main class="container">
    <section class="contact-content">
        <div class="contact-info">
            <h3>İletişime Geçin</h3>
            <p>Herhangi bir soru, proje işbirliği veya sadece merhaba demek için benimle iletişime geçmekten çekinmeyin. Yeni projeler ve fırsatları tartışmaya her zaman açığım.</p>
            
            <div class="contact-details">
                <div class="contact-item">
                    <h4>E-posta</h4>
                    <p><a href="mailto:yamacahmetcan.gmail.com">yamacahmetcan.gmail.com</a></p>
                </div>
                
                <div class="contact-item">
                    <h4>Konum</h4>
                    <p>Ankara, Türkiye</p>
                </div>
                
                <div class="contact-item">
                    <h4>Sosyal Medya</h4>
                    <div class="social-icons">
                        <a href="https://github.com/yamacacan" class="social-icon">GitHub</a>
                        <a href="https://www.linkedin.com/in/ahmet-can-yamaç-9b373b252/" class="social-icon">LinkedIn</a>
                        <a href="#" class="social-icon">Twitter</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="contact-form-container">
            <h3>Bana Mesaj Gönderin</h3>
            
            <?php if ($message_sent): ?>
                <div id="form-success" class="form-success">
                    <h3>Teşekkürler!</h3>
                    <p>Mesajınız başarıyla gönderildi. En kısa sürede size geri dönüş yapacağım.</p>
                </div>
            <?php else: ?>
                <?php if (!empty($errors)): ?>
                    <div class="error-container">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form id="contact-form" class="contact-form" method="POST" action="">
                    <div class="form-group">
                        <label for="name">İsim Soyisim</label>
                        <input type="text" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
                        <span class="error-message" id="name-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-posta</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                        <span class="error-message" id="email-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Konu</label>
                        <input type="text" id="subject" name="subject" value="<?php echo isset($subject) ? $subject : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Mesaj</label>
                        <textarea id="message" name="message" rows="5" required><?php echo isset($message) ? $message : ''; ?></textarea>
                        <span class="error-message" id="message-error"></span>
                    </div>
                    
                    <button type="submit" name="submit_message" class="btn primary-btn">Mesaj Gönder</button>
                </form>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once('includes/footer.php'); ?> 