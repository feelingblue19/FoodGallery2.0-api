<?php

namespace App\Controller;
use Controller;

class Recipe extends Controller {
    private $modelName = 'App\Model\Recipe';

    public function tampil() {
        $recipe = array();
        $recipe['data'] = array();
        $datas = $this->model($this->modelName)->all();

        foreach ($datas as $data) {
            array_push($recipe['data'], $this->transform($data));
        }
        
        echo json_encode($recipe);
    }

    public function tambah() {
        if (!isset($_FILES['foto'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 'recipe gagal dibuat123'
            ]);
            return;
        }
    
        if (is_null($namaFile = $this->uploadFile($_FILES['foto']))) {
            http_response_code(500);
            echo json_encode([
                'status' => 'recipe gagal dibuat'
            ]);
            return;
        }

        date_default_timezone_set('Asia/Jakarta');
    
        $recipe = $this->model($this->modelName);
        $recipe->judul = $_POST['judul'];
        $recipe->foto = $namaFile;
        $recipe->konten = $_POST['konten'];
        $recipe->user_id = $_POST['user_id'];
        $recipe->tanggal = date('Y-m-d');
        $recipe->created_at = date('Y-m-d H:i:s');
        $recipe->updated_at = date('Y-m-d H:i:s');

        try {
            if ($recipe->create()) {
                http_response_code(201);
                echo json_encode([
                    'status' => 'recipe berhasil dibuat',
                    'data' => $recipe
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'recipe gagal dibuat'
                ]);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function detail($id) {
        try {
            $data = $this->cari($id);

            if ($data) {
                http_response_code(200);
                echo json_encode($this->transform($data));
            }
            else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'recipe tidak ditemukan'
                ]);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function hapus($id) {
        try {
            $recipe = $this->cari($id);

            if (!$recipe) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'recipe tidak ditemukan'
                ]);
                return;
            }

            if ($recipe->destroy()) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'recipe berhasil dihapus'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'recipe gagal dihapus'
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function cari($id) {
        try {
            $data = $this->model($this->modelName)->find($id);

            if ($data) {
                $recipe = $this->model($this->modelName);
                foreach ($data as $key => $value) {
                    $recipe->{$key} = $value;
                }
                return $recipe;
            }
            
            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function ubah($id) {
        try {
            $recipe = $this->cari($id);
            if (!$recipe) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'recipe tidak ditemukan'
                ]);
                return;
            }

            $namaFile = isset($_FILES['foto']) ? $this->uploadFile($_FILES['foto']) : false;

            if (is_null($namaFile)) {
                http_response_code(500);
                echo json_encode(['status' => 'recipe gagal diupdate']); 
                return;
            }

            date_default_timezone_set('Asia/Jakarta');

            $recipe->judul = $_POST['judul'];
            if ($namaFile)
                $recipe->foto = $namaFile;
            $recipe->konten = $_POST['konten'];
            $recipe->updated_at = date('Y-m-d H:i:s');

            if ($recipe->update()) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'recipe berhasil diupdate',
                    'data' => $this->transform($recipe)
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'recipe gagal diupdate'
                ]); 
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function uploadFile($file) {
        $namaFile = $file['name'];
        $direktori = '../public/uploads/'; 
        $tempName = $file['tmp_name'];
        
        if (move_uploaded_file($tempName, $direktori . $namaFile))
            return '/uploads/' . $namaFile;
        else 
            return null;
    }

    public function transform($data) {

        if (is_array($data))
            extract($data);
        
        $dataItem = array(
            'id_recipe' => is_array($data) ? $id_recipe : $data->id_recipe,
            'judul' => is_array($data) ? $judul : $data->judul,
            'tanggal' => is_array($data) ? $tanggal : $data->tanggal,
            'foto' => is_array($data) ? $foto : $data->foto,
            'konten' => is_array($data) ? $konten : $data->konten,
            'user' => array(
                'id' => is_array($data) ? $user_id : $data->user_id,
                'first_name' => is_array($data) ? $first_name : $data->first_name,
                'last_name' => is_array($data) ? $last_name : $data->last_name
            ),
            'created_at' => is_array($data) ? $created_at : $data->created_at,
            'updated_at' => is_array($data) ? $updated_at : $data->updated_at
        );

        return $dataItem;
    }
}