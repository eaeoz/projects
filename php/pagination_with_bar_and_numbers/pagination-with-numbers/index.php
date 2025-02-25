<?php
include_once("baglan.php");
$ToplamKayitSayisiSorgusu = $db->prepare("SELECT * FROM urunler");
$ToplamKayitSayisiSorgusu->execute();
$ToplamKayitSayisi = $ToplamKayitSayisiSorgusu->rowCount();
// Number of records per page
$sayfaBasinaKayit = 5;
// Calculate total number of pages
$toplamSayfa = ceil($ToplamKayitSayisi / $sayfaBasinaKayit);
// Current page number
$sayfa = isset($_GET['page']) ? $_GET['page'] : 1;
// Calculate start record number for the current page
$baslangic = ($sayfa - 1) * $sayfaBasinaKayit;
// SQL query with LIMIT clause to retrieve paginated data
$urunlerSorgusu = $db->prepare("SELECT * FROM urunler LIMIT :baslangic, :sayfaBasinaKayit");
$urunlerSorgusu->bindParam('baslangic', $baslangic, PDO::PARAM_INT);
$urunlerSorgusu->bindParam('sayfaBasinaKayit', $sayfaBasinaKayit, PDO::PARAM_INT);
$urunlerSorgusu->execute();
// Fetch paginated data
$urunler = $urunlerSorgusu->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sayfalama Uygulamasi</title>
</head>

<body>
    <table>
        <tr>
            <th>ID</th>
            <th>Urun Adi</th>
            <th>Urun Fiyati</th>
            <th>Para Birimi</th>
        </tr>
        <?php foreach ($urunler as $urun): ?>
        <tr>
            <td><?= $urun['id'] ?></td>
            <td><?= $urun['urunadi'] ?></td>
            <td><?= $urun['urunfiyati'] ?></td>
            <td><?= $urun['parabirimi'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div class="pagination">
        <!-- Start button -->
        <?php if ($sayfa > 1): ?>
        <a href="?page=1">First</a>
        <?php endif; ?>

        <!-- Previous page link -->
        <?php if ($sayfa > 1): ?>
        <a href="?page=<?= $sayfa - 1 ?>">« Prev</a>
        <?php endif; ?>

        <!-- Page numbers -->
        <?php
        $startPage = max(1, $sayfa - floor($sayfaBasinaKayit / 2));
        $endPage = min($toplamSayfa, $startPage + $sayfaBasinaKayit - 1);
        for ($i = $startPage; $i <= $endPage; $i++): ?>
        <a href="?page=<?= $i ?>" <?= ($i == $sayfa) ? 'class="active"' : '' ?>>
            <?= $i ?>
        </a>
        <?php endfor; ?>

        <!-- Next page link -->
        <?php if ($sayfa < $toplamSayfa): ?>
        <a href="?page=<?= $sayfa + 1 ?>">Next »</a>
        <?php endif; ?>

        <!-- End button -->
        <?php if ($sayfa < $toplamSayfa): ?>
        <a href="?page=<?= $toplamSayfa ?>">Last</a>
        <?php endif; ?>
    </div>
</body>

</html>
<?php
$db = null;
?>