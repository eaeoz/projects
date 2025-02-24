<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=extra_egitim;charset=utf8", "root", "");
    // echo "Baglanti basarili <br>";
} catch (PDOException $e) {
    die("Baglanti basarisiz: " . $e->getMessage() . "<br>");
    exit;
}