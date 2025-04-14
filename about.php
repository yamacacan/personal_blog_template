<?php
$page_title = "Hakkımda";
require_once('includes/functions.php');
require_once('includes/header.php');

// Random placeholder images
$placeholder_images = [
    'https://source.unsplash.com/random/600x800/?developer',
    'https://source.unsplash.com/random/600x800/?programmer',
    'https://source.unsplash.com/random/600x800/?coder'
];

// Skills data
$skills = [
    ['name' => 'HTML/CSS', 'level' => 90],
    ['name' => 'JavaScript', 'level' => 85],
    ['name' => 'PHP', 'level' => 80],
    ['name' => 'MySQL', 'level' => 75],
    ['name' => 'Bootstrap', 'level' => 95],
    ['name' => 'React', 'level' => 70],
    ['name' => 'Node.js', 'level' => 65],
    ['name' => 'Git', 'level' => 80]
];

// Timeline/Experience data
$experiences = [
    [
        'year' => '2025 - Şimdi',
        'title' => 'Web Developer',
        'company' => '4Dimension',
        'description' => 'Büyük ölçekli web uygulamaları geliştirme ve mimari tasarım. PHP, JavaScript ve React kullanarak modern web uygulamaları oluşturma.'
    ]
];

// Education data
$education = [
    [
        'year' => '2021 - 2026',
        'degree' => 'Yazılım Mühendisliği',
        'institution' => 'Ankara Yıldıırm Beyazıt Üniversitesi',
        'description' => 'Yazılım geliştirme, algoritma tasarımı, veri yapıları ve bilgisayar ağları üzerine kapsamlı eğitim.'
    ]
  
];
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="<?php echo $placeholder_images[0]; ?>" alt="Ahmet Can Yamaç" class="rounded-circle img-fluid" style="width: 180px; height: 180px; object-fit: cover;">
                    <h5 class="my-3">Ahmet Can Yamaç</h5>
                    <p class="text-muted mb-1">Web Developer & Technical Writer</p>
                    <p class="text-muted mb-4">Ankara, Türkiye</p>
                    <div class="d-flex justify-content-center mb-2">
                        <a href="blog.php" class="btn btn-primary me-2"><i class="fas fa-envelope me-1"></i> İletişim</a>
                        
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0"><i class="fas fa-envelope me-2"></i>Email</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">yamacahmetcan@gmail.com</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0"><i class="fas fa-phone me-2"></i>Telefon</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">(555) 123-4567</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Adres</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">Ankara, Türkiye</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-code me-2"></i>Yazılım Becerileri</h6>
                    
                    <?php foreach ($skills as $skill): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><?php echo $skill['name']; ?></span>
                            <span><?php echo $skill['level']; ?>%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $skill['level']; ?>%;" 
                                 aria-valuenow="<?php echo $skill['level']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-user me-2"></i>Hakkımda</h5>
                    <p class="text-muted">
                        Merhaba, ben Ahmet Can Yamaç. Web geliştirme ve yazılım teknolojileri konusunda 5+ yıllık deneyime sahip bir geliştiriciyim. 
                        Front-end ve back-end teknolojilerinde uzmanlaşmış olup, özellikle PHP, JavaScript ve modern web framework'leri konusunda derinlemesine bilgi sahibiyim.
                    </p>
                    <p class="text-muted">
                        Bu blogda, öğrendiklerimi paylaşıyor ve teknoloji dünyasındaki gelişmeleri takip ediyorum. Amacım, hem kendi bilgilerimi tazelemek 
                        hem de diğer geliştiricilere yardımcı olabilecek içerikler üretmek.
                    </p>
                    <p class="text-muted">
                        Yazılım geliştirme dışında, teknoloji trendlerini takip etmek, açık kaynak projelere katkıda bulunmak ve 
                        yeni programlama dilleri/framework'ler öğrenmek ilgi alanlarım arasında yer alıyor.
                    </p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><i class="fas fa-briefcase me-2"></i>Deneyim</h5>
                            
                            <div class="accordion" id="experienceAccordion">
                                <?php foreach ($experiences as $index => $exp): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                        <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse<?php echo $index; ?>" 
                                                aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                                aria-controls="collapse<?php echo $index; ?>">
                                            <div>
                                                <strong><?php echo $exp['title']; ?></strong> - <?php echo $exp['company']; ?>
                                                <div class="text-muted small"><?php echo $exp['year']; ?></div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                                         aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#experienceAccordion">
                                        <div class="accordion-body">
                                            <?php echo $exp['description']; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><i class="fas fa-graduation-cap me-2"></i>Eğitim</h5>
                            
                            <div class="accordion" id="educationAccordion">
                                <?php foreach ($education as $index => $edu): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="eduHeading<?php echo $index; ?>">
                                        <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#eduCollapse<?php echo $index; ?>" 
                                                aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                                aria-controls="eduCollapse<?php echo $index; ?>">
                                            <div>
                                                <strong><?php echo $edu['degree']; ?></strong> - <?php echo $edu['institution']; ?>
                                                <div class="text-muted small"><?php echo $edu['year']; ?></div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="eduCollapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                                         aria-labelledby="eduHeading<?php echo $index; ?>" data-bs-parent="#educationAccordion">
                                        <div class="accordion-body">
                                            <?php echo $edu['description']; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-certificate me-2"></i>Sertifikalar & Başarılar</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">2021</h6>
                                    <h5 class="card-title">React Developer Sertifikası</h5>
                                    <p class="card-text">Modern React ve Redux kullanarak web uygulamaları geliştirme konusunda kapsamlı eğitim.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">2020</h6>
                                    <h5 class="card-title">PHP Advanced Developer</h5>
                                    <p class="card-text">PHP ile ileri düzey web uygulamaları geliştirme ve güvenlik konularında uzmanlaşma.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-comments me-2"></i>İletişime Geç</h5>
                    <p>Projeleriniz veya sorularınız için benimle iletişime geçebilirsiniz.</p>
                    <a href="contact" class="btn btn-primary">İletişim Formuna Git</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?> 