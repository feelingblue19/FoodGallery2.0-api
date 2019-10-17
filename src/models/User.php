<?php

namespace App\Model;

class User {
    private $table = 'users';
    private $db;

    public function __construct()
    {
        $this->db = new \Database;
    }

    public function cekEmailUser($data) {
        $this->db->query("SELECT * FROM {$this->table} WHERE email = :email");

        $this->db->bind('email', $data['email']);

        return $this->db->single();
    }

    public function createUser($data, $email_token) {
        $query = "INSERT INTO {$this->table}
                VALUES (
                    NULL,
                    :first_name,
                    :last_name,
                    :email,
                    :email_token,
                    NULL,
                    :password,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                )";

        $this->db->query($query);

        $this->db->bind('first_name', $data['first_name']);
        $this->db->bind('last_name', $data['last_name']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('email_token', $email_token);
        $this->db->bind('password', password_hash($data['password'], PASSWORD_BCRYPT));

        $this->db->execute();

        return $this->db->rowCount();
    }

    public function cekUserdanToken($email, $token) {
        $this->db->query("SELECT email, email_token, email_verified_at FROM {$this->table} WHERE email = :email and email_token = :token");

        $this->db->bind('email', $email);
        $this->db->bind('token', $token);

        return $this->db->single(); 
    }

    public function verifikasiUser($data) {
        $query = "UPDATE {$this->table} SET
                email_token = NULL,
                email_verified_at = CURRENT_TIMESTAMP
                WHERE email = :email";

        $this->db->query($query);

        $this->db->bind('email', $data['email']);

        $this->db->execute();
        
        return $this->db->rowCount();

    }
}