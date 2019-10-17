<?php

namespace App\Model;

class Recipe {

    private $table = 'recipes';
    private $db;

    //recipe database property
    public $id_recipe;
    public $judul;
    public $foto;
    public $konten;
    public $user_id;
    public $tanggal;
    public $created_at;
    public $updated_at;

    public function __construct()
    {
        $this->db = new \Database;
    }

    public function all() {
        $this->db->query("SELECT r.*, u.first_name, u.last_name FROM {$this->table} r
                        JOIN users u
                        ON (r.user_id = u.id)");

        return $this->db->resultSet();
    }

    public function create() {
        $query = "INSERT INTO {$this->table} 
                VALUES(
                    NULL,
                    :judul,
                    :tanggal,
                    :foto,
                    :konten,
                    :user_id,
                    :created_at,
                    :updated_at
                )";

        $this->db->query($query);

        $this->db->bind('judul', $this->judul);
        $this->db->bind('foto', $this->foto);
        $this->db->bind('konten', $this->konten);
        $this->db->bind('user_id', $this->user_id);
        $this->db->bind('tanggal', $this->tanggal);
        $this->db->bind('created_at', $this->created_at);
        $this->db->bind('updated_at', $this->updated_at);

        $this->db->execute();

        if ($this->db->rowCount() > 0) {
            $this->id_recipe = $this->db->lastID();
            return true;
        }
        else 
            return false;
    }

    public function destroy() {
        $this->db->query("DELETE FROM {$this->table} WHERE id_recipe = :id");

        $this->db->bind('id', $this->id_recipe);

        $this->db->execute();
        
        if ($this->db->rowCount()) 
            return true;
        else 
            return false;
    }

    public function find($id) {
        $this->db->query("SELECT r.*, u.first_name, u.last_name FROM {$this->table} r
                        JOIN users u
                        ON (r.user_id = u.id) 
                        WHERE r.id_recipe = :id");

        $this->db->bind('id', $id);

        return $this->db->single();
    }

    public function update() {
        $query = "UPDATE {$this->table} SET
                judul = :judul,
                konten = :konten,
                updated_at = :updated_at";

        $query .= $this->foto ? ', foto = :foto' : null;
        $query .= ' WHERE id_recipe = :id';

        $this->db->query($query);

        if ($this->foto)
            $this->db->bind('foto', $this->foto);

        $this->db->bind('judul', $this->judul);
        $this->db->bind('id', $this->id_recipe);
        $this->db->bind('konten', $this->konten);
        $this->db->bind('updated_at', $this->updated_at);

        $this->db->execute();
        
        if ($this->db->rowCount() > 0)
            return true;
        else 
            return false;
    }

}