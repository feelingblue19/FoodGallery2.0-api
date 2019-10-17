<?php

namespace App\Controller;
use Controller;

class Review extends Controller{

    private $modelName = 'App\Model\Review';

    public function tampil() {
        $reviews = array();
        $reviews['data'] = array();
        $datas = $this->model($this->modelName)->getAllReviews();

        foreach ($datas as $data) {
            extract($data);
            $dataItem = array(
                'id' => $id,
                'judul' => $judul,
                'lokasi' => $lokasi,
                'tanggal' => $tanggal,
                'harga' => $harga,
                'foto' => $foto,
                'konten' => $konten,
                'user' => array(
                    'id' => $user_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name
                ),
                'created_at' => $created_at,
                'updated_at' => $updated_at
            );

            array_push($reviews['data'], $dataItem);
            
        }
        
        echo json_encode($reviews);
    }

    public function tambah() {
        if (isset($_FILES['foto'])) {
            if ($namaFile = $this->uploadFile($_FILES['foto'])) {
                if ($this->model($this->modelName)->postReview($_POST, $namaFile) > 0) {
                    echo json_encode([
                        'status' => 'review berhasil dibuat'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'review gagal dibuat'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'review gagal dibuat'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'review gagal dibuat'
            ]);
        }
    }

    public function detail($id) {
        $data = $this->cari($id);
        
        if ($data)
            echo json_encode($data);
        else {
            echo json_encode([
                'status' => 'review tidak ditemukan'
            ]);
        }
    }

    public function hapus($id) {
        $data = $this->cari($id);
        if (!$data) {
            echo json_encode([
                'status' => 'review tidak ditemukan'
            ]);
            return;
        }

        if ($this->model($this->modelName)->deleteReviewByID($id) > 0) {
            echo json_encode([
                'status' => 'review berhasil dihapus'
            ]);
        } else {
            echo json_encode([
                'status' => 'review gagal dihapus'
            ]);
        }
    }

    public function cari($id) {
        $data = $this->model($this->modelName)->getReviewByID($id);
        return $data;
    }

    public function ubah($id) {
        $data = $this->cari($id);
        if (!$data) {
            echo json_encode([
                'status' => 'review tidak ditemukan'
            ]);
            return;
        }

        // ada file yang akan diupload
        if (isset($_FILES['foto'])) {
            if ($namaFile = $this->uploadFile($_FILES['foto'])) {
                if ($this->model($this->modelName)->updateReview($id, $_POST, $namaFile) > 0) {
                    echo json_encode([
                        'status' => 'review berhasil diupdate'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'review gagal diupdate',
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'review gagal diupdate'
                ]);
            }
        } else {
            //tidak ada file yang akan diupload
            if ($this->model($this->modelName)->updateReview($id, $_POST, false) > 0) {
                echo json_encode([
                    'status' => 'review berhasil diupdate'
                ]);
            } else {
                echo json_encode([
                    'status' => 'review gagal diupdate'
                ]);
            }
        }
    }

    public function uploadFile($file) {
        $namaFile = $file['name'];
        $direktori = '../public/uploads/'; 
        $tempName = $file['tmp_name'];
        
        if (move_uploaded_file($tempName, $direktori . $namaFile))
            return '/uploads/' . $namaFile;
        else 
            return false;
    }
}