<?php
// db.php dosyasını dahil et (veritabanı bağlantısı için)
require_once 'baglan.php';

// Hata mesajı fonksiyonu
function showError($message)
{
    echo "<div class='error-container'><h2>$message</h2></div>";
    exit;
}

// ID parametresini al ve filtrele
$esya_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$esya_id) {
    showError("Geçersiz eşya ID'si!");
}

try {
    // Veritabanından eşya bilgilerini çek
    $sorgu = $db->prepare("SELECT id, adi, url FROM esyalar WHERE id = :id");
    $sorgu->bindParam(':id', $esya_id, PDO::PARAM_INT);
    $sorgu->execute();

    $esya = $sorgu->fetch(PDO::FETCH_ASSOC);

    if (!$esya) {
        showError("Eşya bulunamadı!");
    }
} catch (PDOException $e) {
    showError("Veritabanı hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($esya['adi']) ?> Detayları</title>
    <link rel="stylesheet" href="./css/style_detay.css">
</head>

<body>
    <div class="esya-container">
        <div class="esya-header">
            <h1 class="esya-title"><?= htmlspecialchars($esya['adi']) ?></h1>
        </div>

        <div class="esya-info">
            <span class="info-label">Eşya ID</span>
            <span class="info-value">#<?= htmlspecialchars($esya['id']) ?></span>
        </div>

        <div class="esya-info">
            <span class="info-label">URL Adresi</span>
            <a href="<?= htmlspecialchars($esya['url']) ?>" class="info-value" target="_blank">
                <?= htmlspecialchars($esya['url']) ?>
            </a>
        </div>

        <a href="javascript:history.back()" class="back-link">
            « Geri Dön
        </a>
    </div>
</body>

</html>