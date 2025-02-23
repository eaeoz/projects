<?php

include_once("baglan.php");

?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Sıralanabilir Eşya Listesi</title>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php

    $sayfaBasinaKayit = isset($_GET['per_page']) ? filter_var($_GET['per_page'], FILTER_VALIDATE_INT, [
        'options' => ['default' => 10, 'min_range' => 1]
    ]) : 10;
    $sayfa = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT, [
        'options' => ['default' => 1, 'min_range' => 1]
    ]) : 1;
    $baslangic = ($sayfa - 1) * $sayfaBasinaKayit;

    // Veri Çekme
    $toplamKayit = $db->query("SELECT COUNT(id) FROM esyalar")->fetchColumn();
    $sorgu = $db->prepare("SELECT * FROM esyalar ORDER BY sirano ASC LIMIT :limit OFFSET :offset");
    $sorgu->bindValue(':limit', $sayfaBasinaKayit, PDO::PARAM_INT);
    $sorgu->bindValue(':offset', $baslangic, PDO::PARAM_INT);
    $sorgu->execute();
    $esyalar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
    $toplamSayfa = ceil($toplamKayit / $sayfaBasinaKayit);
    ?>

    <?php if (!empty($esyalar)): ?>
        <table id="esyaTablosu">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Eşya Adı</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($esyalar as $esya):
                    echo "<tr>";
                    foreach ($esya as $key => $value) {
                        $keys = ['id', 'adi'];
                        if (in_array($key, $keys)) {
                            echo "<td>$value</td>";
                        }
                    }
                    echo "</tr>";
                endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <!-- Mevcut sayfa linkleri -->
            <?php if ($sayfa > 1): ?>
                <a href="?page=<?= $sayfa - 1 ?>&per_page=<?= $sayfaBasinaKayit ?>">« Önceki</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $toplamSayfa; $i++): ?>
                <a href="?page=<?= $i ?>&per_page=<?= $sayfaBasinaKayit ?>" <?= ($i == $sayfa) ? 'class="active"' : '' ?>>
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($sayfa < $toplamSayfa): ?>
                <a href="?page=<?= $sayfa + 1 ?>&per_page=<?= $sayfaBasinaKayit ?>">Sonraki »</a>
            <?php endif; ?>

            <!-- Sağ köşeye combobox ekle -->
            <div class="per-page">
                <select id="perPageSelect">
                    <option value="5" <?= $sayfaBasinaKayit == 5 ? 'selected' : '' ?>>5</option>
                    <option value="10" <?= $sayfaBasinaKayit == 10 ? 'selected' : '' ?>>10</option>
                    <option value="15" <?= $sayfaBasinaKayit == 15 ? 'selected' : '' ?>>15</option>
                </select>
            </div>
        </div>

        <script>
            // Sortable.js Yapılandırması
            Sortable.create(document.querySelector('#esyaTablosu tbody'), {
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    const rows = Array.from(evt.from.children);

                    // Yeni sıralamayı offset ile hesapla
                    const siralamalar = rows.map((row, index) => ({
                        id: parseInt(row.children[0].textContent),
                        sira: baslangicOffset + index + 1 // Kritik düzeltme burada!
                    }));

                    fetch('sirala.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                siralamalar
                            })
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.json();
                        })
                        .then(data => {
                            if (!data.success) throw new Error(data.error);

                            // Sayfayı yenile (yeni sıralamayı görmek için)
                            setTimeout(() => {
                                window.location.reload();
                            }, 300);
                        })
                        .catch(error => {
                            console.error('Hata:', error);
                            alert('Güncelleme başarısız: ' + error.message);
                        });
                }
            });
            const currentPage = <?= $sayfa ?>;
            const baslangicOffset = <?= $baslangic ?>; // (currentPage - 1) * sayfaBasinaKayit
            document.getElementById('perPageSelect').addEventListener('change', function(e) {
                const perPage = e.target.value;
                const url = new URL(window.location.href);

                // Yeni parametreleri ayarla
                url.searchParams.set('per_page', perPage);
                url.searchParams.set('page', 1); // Her değişiklikte 1. sayfaya dön

                window.location.href = url.toString();
            });
        </script>

    <?php else: ?>
        <p style="text-align:center; color:#666; margin:50px 0;">
            Henüz kayıt bulunmamaktadır.
        </p>
    <?php endif; ?>
</body>

</html>
<?php

$db = null;

?>