<?php
include_once("baglan.php");
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Layout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .navbar-dark {
        background-color: #393B3B !important;
    }

    .wrapper {
        display: flex;
        flex: 1;
        position: relative;
    }

    .sidebar {
        width: 250px;
        background: #f8f9fa;
        padding: 20px;
    }

    .main-content {
        flex: 1;
        padding: 20px;
        min-height: calc(100vh - 56px);
        /* Navbar yüksekliğini çıkar */
    }

    footer {
        background: #2c3e50;
        color: #ecf0f1;
        margin-top: auto;
    }

    .footer-col {
        padding: 30px 15px;
    }

    .footer-links a {
        color: #bdc3c7;
        text-decoration: none;
        transition: color 0.3s;
    }

    .footer-links a:hover {
        color: #3498db;
    }

    .social-icons a {
        font-size: 24px;
        margin-right: 15px;
        color: #ecf0f1;
    }

    /* Üst Banner Stilleri */
    .top-banner .bottom-banner {
        background: #f8f9fa;
        padding: 15px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .top-banner img,
    .bottom-banner img {
        max-height: 200px;
        width: auto;
        margin: 0 auto;
        display: block;
    }

    .right-banner {
        width: 250px;
        background: #f8f9fa;
        padding: 15px;
        display: flex;
        flex-direction: column;
    }


    .right-banner img {
        max-width: 100%;
        height: auto;
        object-fit: contain;
        transform: rotate(90deg) scale(3.9) translateX(47%);
        position: relative;

    }

    @media (max-width: 992px) {
        .wrapper {
            flex-wrap: wrap;
        }

        .sidebar {
            width: 100%;
            order: 2;
        }

        .main-content {
            min-height: auto;
            order: 1;
            width: 100%;
        }

        .right-banner {
            display: none;
        }

        .right-banner-img {
            display: none;
        }
    }
    </style>
</head>

<body>

    <?php

    try {
        $Sorgu = $db->prepare("SELECT * FROM bannerlar ORDER BY gosterimsayisi ASC LIMIT 3");
        $Sorgu->execute();
        $Banner = $Sorgu->fetchAll(PDO::FETCH_ASSOC);
        $BanerSayisi = $Sorgu->rowCount();
        // banerlerin sadece 3 tane olmasi gerek
        if (($BanerSayisi > 0)) {
            $BirinciBaner = $Banner[0]['id'];
            $IkinciBaner = $Banner[1]['id'];
            $UcuncuBaner = $Banner[2]['id'];
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>

    <?php if (isset($Banner[0])): ?>
    <div class="top-banner mb-3">
        <div class="container">
            <a href="<?= $Banner[0]['banner_link'] ?? '#' ?>">
                <img src="<?= $Banner[0]['bannerdosyasi'] ?>" alt="<?= $Banner[0]['alt_text'] ?? '' ?>"
                    class="img-fluid">
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navbar (alt menu olmadan) -->
    <!-- 
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Logo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Anasayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">İletişim</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav> 
    -->

    <!-- Navbar (alt menu ile) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class=" container-fluid">
            <!-- Logo/Marka -->
            <a class="navbar-brand" href="#"><img
                    src="https://cdn.pixabay.com/photo/2017/01/08/21/37/flame-1964066_640.png" class="img-fluid"
                    width="30" height="30" alt="Logo"></a>

            <!-- Hamburger Menü Butonu -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar İçeriği -->
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <!-- Normal Menü Öğesi -->
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Anasayfa</a>
                    </li>

                    <!-- Dropdown Menü 1 -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Ürünler
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Elektronik</a></li>
                            <li><a class="dropdown-item" href="#">Giyim</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Kampanyalar</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Menü 2 -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Hizmetler
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Danışmanlık</a></li>
                            <li><a class="dropdown-item" href="#">Destek</a></li>
                            <li><a class="dropdown-item" href="#">Eğitim</a></li>
                        </ul>
                    </li>

                    <!-- Basit Menü Öğesi -->
                    <li class="nav-item">
                        <a class="nav-link" href="#">İletişim</a>
                    </li>
                </ul>

                <!-- Sağ Taraf Öğeleri -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-person"></i> Giriş Yap</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="wrapper">
        <!-- Sol Sidebar -->
        <div class="sidebar">
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action">Menu 1</a>
                <a href="#" class="list-group-item list-group-item-action">Menu 2</a>
                <a href="#" class="list-group-item list-group-item-action">Menu 3</a>
                <a href="#" class="list-group-item list-group-item-action">Menu 4</a>
            </div>
        </div>

        <!-- Ana İçerik -->
        <div class="main-content">
            <h1 class="mb-4">Sayfa Başlığı</h1>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Demo İçerik 1</h5>
                    <p class="card-text">Lorem ipsum dolor sit amet...</p>
                </div>
            </div>

            <!-- Uzun içerik örneği -->
            <div style="height: 1500px; background: #f0f0f0; margin: 20px 0;">
                Uzun içerik alanı (Scroll için test)
            </div>
        </div>

        <!-- Sağ Reklam Banner -->
        <div class="right-banner">
            <?php if (isset($Banner[1])): ?>
            <a href="<?= $Banner[1]['banner_link'] ?? '#' ?>">
                <img src="<?= $Banner[1]['bannerdosyasi'] ?>" alt="<?= $Banner[1]['alt_text'] ?? '' ?>">
            </a>
            <?php endif; ?>
        </div>
    </div>


    <div class="bottom-banner">
        <?php if (isset($Banner[2])): ?>
        <a href="<?= $Banner[2]['banner_link'] ?? '#' ?>">
            <img src="<?= $Banner[2]['bannerdosyasi'] ?>" alt="<?= $Banner[2]['alt_text'] ?? '' ?>">
        </a>
        <?php endif; ?>
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <!-- Hakkımızda -->
                <div class="col-md-3 footer-col">
                    <h5>Hakkımızda</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Şirket Tarihçesi</a></li>
                        <li><a href="#">Misyon & Vizyon</a></li>
                        <li><a href="#">Kariyer</a></li>
                    </ul>
                </div>

                <!-- Hizmetler -->
                <div class="col-md-3 footer-col">
                    <h5>Hizmetler</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Teknik Destek</a></li>
                        <li><a href="#">Sıkça Sorulan Sorular</a></li>
                        <li><a href="#">Garanti Koşulları</a></li>
                    </ul>
                </div>

                <!-- İletişim -->
                <div class="col-md-3 footer-col">
                    <h5>İletişim</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt"></i> İstanbul, Türkiye</li>
                        <li><i class="fas fa-phone"></i> 0 (212) 123 45 67</li>
                        <li><i class="fas fa-envelope"></i> info@example.com</li>
                    </ul>
                </div>

                <!-- Sosyal Medya -->
                <div class="col-md-3 footer-col">
                    <h5>Sosyal Medya</h5>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center py-3 border-top">
                <p class="mb-0">© 2023 Tüm Hakları Saklıdır</p>
            </div>
        </div>
    </footer>


    <?php
    // tum banerler mevcut ise gosterimsayisini 1 arttir
    if (isset($Banner)) {

        $ids = [$BirinciBaner, $IkinciBaner, $UcuncuBaner];

        $placeholders = implode(',', $ids); // 1,2,4

        $ReklamGuncelle = $db->prepare("UPDATE bannerlar SET gosterimsayisi = gosterimsayisi + 1 WHERE id IN ($placeholders)");

        $ReklamGuncelle->execute();
    }
    ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>