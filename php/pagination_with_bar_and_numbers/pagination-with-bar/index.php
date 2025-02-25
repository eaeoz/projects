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

    <div class="pagination-bar-container">
        <div class="pagination-info">
            <span id="current-page"><?= $sayfa ?>/<?= $toplamSayfa ?></span>
        </div>
        <div class="pagination-bar">
            <div class="bar-handle"></div>
            <div class="bar-background"></div>
        </div>
    </div>

    <script>
    const paginationBar = document.querySelector('.pagination-bar-container');
    const barHandle = document.querySelector('.bar-handle');
    const totalPages = <?php echo $toplamSayfa; ?>;
    const barWidth = paginationBar.offsetWidth;
    const currentPageSpan = document.getElementById('current-page');
    // Retrieve stored page number from localStorage
    let currentPage = localStorage.getItem('currentPage') ? parseInt(localStorage.getItem('currentPage')) : 1;
    // Function to update handle position
    const updateHandlePosition = () => {
        const pageWidth = barWidth / (totalPages - 1);
        barHandle.style.left = `${(currentPage - 1) * pageWidth}px`;
        currentPageSpan.textContent = `${currentPage}/${totalPages}`;
    };
    // Initialize position on load
    updateHandlePosition();
    paginationBar.addEventListener('mousedown', (e) => {
        const rect = paginationBar.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const pageWidth = barWidth / (totalPages - 1);
        currentPage = Math.round(x / pageWidth) + 1;
        updateHandlePosition();
        const onMouseMove = (e) => {
            const rect = paginationBar.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const pageWidth = barWidth / (totalPages - 1);
            const steps = Math.round(x / pageWidth);
            currentPage = steps + 1;
            const handleLeft = steps * pageWidth;
            const maxLeft = barWidth - barHandle.offsetWidth;
            const minLeft = 0;
            if (handleLeft >= maxLeft) {
                barHandle.style.left = `${maxLeft}px`;
                currentPage = totalPages;
            } else if (handleLeft < minLeft) {
                barHandle.style.left = `${minLeft}px`;
                currentPage = 1;
            } else {
                barHandle.style.left = `${handleLeft}px`;
            }
            currentPageSpan.textContent = `${currentPage}/${totalPages}`;
        };
        const onMouseUp = (e) => {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
            // Store current page in localStorage
            localStorage.setItem('currentPage', currentPage);
            if (e.clientX !== e.screenX) {
                window.location.href = `?page=${currentPage}`;
            }
        };
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });
    </script>
</body>

</html>
<?php
$db = null;
?>