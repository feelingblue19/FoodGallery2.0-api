<?php 

namespace App\Model;
use Database;

class Review {
    private $table = 'reviews';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllReviews() {
        $this->db->query("SELECT r.*, u.first_name, u.last_name FROM {$this->table} r
                        JOIN users u 
                        ON (r.user_id = u.id)");
                        
        return $this->db->resultSet();
    }

    public function postReview($data, $foto) {
        $query = "INSERT INTO {$this->table} 
                VALUES(
                    NULL,
                    :judul,
                    :lokasi,
                    NOW(),
                    :harga,
                    :foto,
                    :konten,
                    :user_id,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                )";

        $this->db->query($query);

        $this->db->bind('judul', $data['judul']);
        $this->db->bind('lokasi', $data['lokasi']);
        $this->db->bind('harga', $data['harga']);
        $this->db->bind('foto', $foto);
        $this->db->bind('konten', $data['konten']);
        $this->db->bind('user_id', $data['user_id']);

        $this->db->execute();

        return $this->db->rowCount();
    }

    public function deleteReviewByID($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind('id', $id);

        $this->db->execute();
        
        return $this->db->rowCount();
    }

    public function getReviewByID($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind('id', $id);

        return $this->db->single();
    }

    public function updateReview($id, $data, $foto) {
        $query = "UPDATE {$this->table} SET
                         judul = :judul,
                         lokasi = :lokasi,
                         harga = :harga,
                         konten = :konten,
                         updated_at = CURRENT_TIMESTAMP()";

        $query .= $foto ? ", foto = :foto" : null;

        $query .= " WHERE id = :id";

        $this->db->query($query);

        if ($foto)
            $this->db->bind('foto', $foto);
            
        $this->db->bind('judul', $data['judul']);
        $this->db->bind('id', $id);
        $this->db->bind('lokasi', $data['lokasi']);
        $this->db->bind('harga', $data['harga']);
        $this->db->bind('konten', $data['konten']);

        $this->db->execute();
        
        return $this->db->rowCount();
    }
}