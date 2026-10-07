<?php
// update_status.php
require_once 'config/database.php';
require_once 'classes/ProdukHukum.php';

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    $produk = new ProdukHukum($db);

    $id = $_POST['id'];
    $status_baru = $_POST['status_baru'];
    $catatan = $_POST['catatan'];

    if ($produk->updateStatus($id, $status_baru, $catatan)) {
        header("Location: detail_produk.php?id=" . $id);
    } else {
        echo "<script>alert('Gagal memperbarui status'); window.history.back();</script>";
    }
}
?>