    </main>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h3>Ahmet Can Yamaç</h3>
                    <p>Web Developer & Technical Writer</p>
                    <p>Ankara, Turkey</p>
                </div>
                <div class="footer-links">
                    <h3>Hızlı Bağlantılar</h3>
                    <ul>
                        <li><a href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>index">Ana Sayfa</a></li>
                        <li><a href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>about">Hakkımda</a></li>
                        <li><a href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>blog">Blog</a></li>
                        <li><a href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>contact">İletişim</a></li>
                        <?php if (isLoggedIn() && isAdmin()): ?>
                        <li><a href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>admin">Admin</a></li>
                        <li><a href="<?php echo isset($page_depth) ? str_repeat('../', $page_depth) : ''; ?>logout">Çıkış</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-social">
                    <h3>Bağlantılar</h3>
                    <div class="social-icons">
                        <a href="https://github.com/yamacacan" class="social-icon">GitHub</a>
                        <a href="https://www.linkedin.com/in/ahmet-can-yamaç-9b373b252/" class="social-icon">LinkedIn</a>
                        <a href="#" class="social-icon">Twitter</a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> Ahmet Can Yamaç. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>
                            
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 
</body>
</html> 