<?php
include_once('baglan.php');
header('Content-Type: application/json');

try {
    $searchTerm = isset($_GET['q']) ? '%' . $_GET['q'] . '%' : '';

    $sorgu = $db->prepare("SELECT id, adi 
                          FROM esyalar 
                          WHERE adi LIKE :search 
                          ORDER BY adi ASC 
                          LIMIT 8");
    $sorgu->bindParam(':search', $searchTerm);
    $sorgu->execute();

    $sonuclar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($sonuclar);
} catch (PDOException $e) {
    echo json_encode([]);
}
