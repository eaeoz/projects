<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=extra_egitim;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Baglanti basarisiz: " . $e->getMessage() . "<br>");
    exit;
}