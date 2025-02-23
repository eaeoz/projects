<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=extra_egitim;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Baglanti basarili <br>";
} catch (PDOException $e) {
    die("Baglanti basarisiz: " . $e->getMessage() . "<br>");
    exit;
}

function filtre($Deger)
{
    $Deger = htmlspecialchars(strip_tags(trim($Deger)));
    return $Deger;
}
