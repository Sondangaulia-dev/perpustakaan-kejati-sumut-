<?php
// classes/ProdukHukum.php
class ProdukHukum {
    private $conn;
    private $table_name = "produk_hukum";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ambil semua data produk hukum
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Simpan draf baru
    public function create($judul, $kategori, $file_name) {
        $query = "INSERT INTO " . $this->table_name . " (judul_rancangan, kategori, status, file_draft) VALUES (:judul, :kategori, 'Drafting', :file)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":judul", $judul);
        $stmt->bindParam(":kategori", $kategori);
        $stmt->bindParam(":file", $file_name);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Update status & catat log
    public function updateStatus($id, $status_baru, $catatan) {
        // 1. Ambil status lama
        $query_get = "SELECT status FROM " . $this->table_name . " WHERE id = :id";
        $stmt_get = $this->conn->prepare($query_get);
        $stmt_get->bindParam(":id", $id);
        $stmt_get->execute();
        $row = $stmt_get->fetch(PDO::FETCH_ASSOC);
        $status_lama = $row['status'];

        // 2. Update status baru
        $query_update = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt_update = $this->conn->prepare($query_update);
        $stmt_update->bindParam(":status", $status_baru);
        $stmt_update->bindParam(":id", $id);
        $stmt_update->execute();

        // 3. Catat ke tabel tracking_logs
        $query_log = "INSERT INTO tracking_logs (produk_hukum_id, catatan, status_lama, status_baru) VALUES (:p_id, :catatan, :s_lama, :s_baru)";
        $stmt_log = $this->conn->prepare($query_log);
        $stmt_log->bindParam(":p_id", $id);
        $stmt_log->bindParam(":catatan", $catatan);
        $stmt_log->bindParam(":s_lama", $status_lama);
        $stmt_log->bindParam(":s_baru", $status_baru);
        return $stmt_log->execute();
    }

    // Ambil detail produk hukum
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ambil riwayat log tracking
    public function getLogs($id) {
        $query = "SELECT * FROM tracking_logs WHERE produk_hukum_id = :id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>