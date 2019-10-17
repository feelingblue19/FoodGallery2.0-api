<?php

namespace App\Controller;

require_once '../src/PHPMailer/src/Exception.php';
require_once '../src/PHPMailer/src/PHPMailer.php';
require_once '../src/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;


class User extends \Controller {
    private $modelName = 'App\Model\User';

    public function login() {
        $user = $this->getUser();
        
        if ($user && $this->cekPassword($user)) {
            if (!is_null($user['email_verified_at'])) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'login sukses'
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'akun belum diverifikasi'
                ]);
            }
        }
        else {
            http_response_code(404);
            echo json_encode([
                'status' => 'email atau password salah'
            ]);
        }  
    }

    public function register() {
        if ($this->getUser()) {
            http_response_code(500);
            echo json_encode([
                'status' => 'akun dengan email tersebut sudah terdaftar'
            ]);
            return;
        }

        if ($token = $this->sendEmail()) {
            if ($this->model($this->modelName)->createUser($_POST, $token) > 0) {
                http_response_code(201);
                echo json_encode([
                    'status' => "akun berhasil dibuat"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => "akun gagal dibuat"
                ]);
            }
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => "akun gagal dibuat"
            ]);
        }
    }

    public function getUser() {
        return $this->model($this->modelName)->cekEmailUser($_POST);
    }

    public function cekPassword($user) {
        if (!$user) 
            return false; 
        
        return password_verify($_POST['password'], $user['password']);
    }

    public function verifikasi($email, $token) {
        $user = $this->model($this->modelName)->cekUserdanToken($email, $token);

        if (is_null($user['email_verified_at']) && $this->model($this->modelName)->verifikasiUser($user)) {
            http_response_code(200);
            echo json_encode([
                'status' => 'akun berhasil diverifikasi'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'akun gagal diverifikasi'
            ]);
        }
    }

    public function sendEmail() {
        $url = BASEURL;
        $token = 'qwertyuiopasdfghjklzxcvbnmqWERTYUIOPASDFGHJKLZXCVBNM1234567890!@#$%^&*()_+';
        $token = str_shuffle($token);
        $token = substr($token, 0, 20);
		
        $mail = new PHPMailer(TRUE);                            
		$mail->isSMTP();                                    
		$mail->Host = SMTP_HOST; 
		$mail->SMTPAuth = true;                             
		$mail->Username = SMTP_UNAME;           
		$mail->Password = SMTP_PASSWORD;                      
		$mail->SMTPSecure = 'tls';                       
		$mail->Port = SMTP_PORT;          
        $mail->setFrom('info@foodgallery.com');
        $mail->addAddress($_POST['email']);
		$mail->isHTML(true);
        $mail->Subject="Verify email Food Gallery";
        $mail->Body = " 
            Halo {$_POST['first_name']} {$_POST['last_name']},<br><br>
            
            Silakan klik link di bawah ini untuk melakukan verifikasi akun anda<br>
            <a href='{$url}/user/verifikasi/{$_POST['email']}/{$token}'>
                {$url}/user/verifikasi/{$_POST['email']}/{$token}
            </a>
        ";

        if ($mail->send()) 
            return $token;
        else 
            return false;
    }
}