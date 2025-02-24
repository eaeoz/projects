<?php
include_once("baglan.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-image: linear-gradient(to bottom, #f0f0f0, #ccc);
        background-attachment: fixed;
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    table {
        border: 4px solid black;
        border-radius: 10px;
        border-collapse: separate;
        padding: 0.1px;
        border-spacing: 0;
        width: 80%;
        margin: 20px auto;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    th,
    td {
        border: none;
        padding: 10px;
        text-align: center;
    }

    table tr {
        transition: background-color 0.2s ease-in-out;
    }

    table tr:hover {
        background-color: rgba(0, 0, 0, 0.2);
    }

    table tr td {
        overflow: hidden;
        transition: transform 0.2s ease-in-out;
    }

    table tr td:hover {
        transform: scale(1.1);
    }

    th {
        background-color: #aaa;
    }

    tr:nth-child(even) {
        background-color: #E9E9E9;
    }

    tr:nth-child(odd) {
        background-color: #fff;
    }

    h1 {
        text-align: center;
        margin-bottom: 10px;
        color: #333;
    }
    </style>
</head>

<body>
    <h1>Urunler</h1>
    <?php

    try {
        $Sorgu = $db->prepare("SELECT * FROM urunler");
        $Sorgu->execute();
        $Urunler = $Sorgu->fetchAll(PDO::FETCH_ASSOC);
        echo " - Sorgu basarili - <br>";
    } catch (Exception $e) {
        die("Baglanti basarisiz: " . $e->getMessage() . "<br>");
        exit;
    }

    echo "<table>";
    $first = true;
    foreach ($Urunler as $Urun) {
        if ($first) {
            echo "<tr>";
            foreach ($Urun as $Key => $Value) {
                echo "<th>" . strtoupper($Key) . "</th>";
            }
            $first = false;
            echo "</tr>";
        }
        echo "<tr>";
        foreach ($Urun as $Key => $Value) {
            echo "<td>" . $Value . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";

    ?>

</body>

</html>
<?php

$db = null;

?>