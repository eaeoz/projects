<?php
include_once 'baglan.php';
header('Content-Type: application/json');

// Hata yönetimi için try-catch
try {
    // JSON veriyi al
    $input = json_decode(file_get_contents('php://input'), true);

    // Veri kontrolü
    if (!isset($input['siralamalar']) || !is_array($input['siralamalar'])) {
        throw new Exception('Geçersiz veri formatı');
    }

    // Veritabanı işlemleri
    $db->beginTransaction();

    // Önceden hazırlanmış sorgu
    $stmt = $db->prepare("UPDATE esyalar SET sirano = :sira WHERE id = :id");

    foreach ($input['siralamalar'] as $siralama) {
        // Güvenlik kontrolü
        $id = filter_var($siralama['id'], FILTER_VALIDATE_INT);
        $sira = filter_var($siralama['sira'], FILTER_VALIDATE_INT);

        if (!$id || !$sira) {
            throw new Exception('Geçersiz ID veya sıra değeri');
        }

        // Sorguyu çalıştır
        $stmt->execute([
            ':sira' => $sira,
            ':id' => $id
        ]);
    }

    $db->commit();

    // Başarılı yanıt
    echo json_encode([
        'success' => true,
        'message' => 'Sıralama başarıyla güncellendi'
    ]);
} catch (PDOException $e) {
    $db->rollBack();
    echo json_encode([
        'success' => false,
        'error' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
