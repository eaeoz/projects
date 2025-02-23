<?php

include_once("baglan.php");

?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Sıralanabilir Eşya Listesi</title>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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
    <div class="search-container">
        <input type="text" id="liveSearch" placeholder="Eşya Ara..." autocomplete="off">
        <div class="search-results" id="searchResults"></div>
    </div>
    <table id="esyaTablosu">
        <thead>
            <tr>
                <th>ID</th>
                <th>Eşya Adı</th>
                <th>URL</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($esyalar as $esya):
                    echo "<tr>";
                    foreach ($esya as $key => $value) {
                        $keys = ['id', 'adi', 'url'];
                        if (in_array($key, $keys)) {
                            if ($key == 'url') {
                                echo "<td><a href='$value' target='_blank'><i class='fa-solid fa-link'></i></a></td>";
                                // you can add else if for different key type
                            } else {
                                echo "<td>$value</td>";
                            }
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
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearch');
        const resultsContainer = document.getElementById('searchResults');
        let searchTimeout;

        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.trim();

            if (searchTerm.length >= 3) {
                searchTimeout = setTimeout(() => {
                    fetch(`arama.php?q=${encodeURIComponent(searchTerm)}`)
                        .then(response => response.json())
                        .then(data => {
                            showResults(data);
                        })
                        .catch(error => {
                            console.error('Arama hatası:', error);
                        });
                }, 300);
            } else {
                resultsContainer.style.display = 'none';
            }
        });

        function showResults(results) {
            resultsContainer.innerHTML = '';

            if (results.length > 0) {
                results.slice(0, 8).forEach(item => {
                    const resultItem = document.createElement('a');
                    resultItem.className = 'search-item';
                    resultItem.href = `esya-detay.php?id=${item.id}`; // URL'i kendinize göre ayarlayın
                    resultItem.innerHTML = `
                    <strong>${item.adi}</strong><br>
                    <small>ID: ${item.id}</small>
                `;
                    resultsContainer.appendChild(resultItem);
                });
            } else {
                const noResults = document.createElement('div');
                noResults.className = 'no-results';
                noResults.textContent = 'Sonuç bulunamadı';
                resultsContainer.appendChild(noResults);
            }

            resultsContainer.style.display = 'block';
        }

        // Dışarı tıklamada kapatma
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                resultsContainer.style.display = 'none';
            }
        });
    });
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