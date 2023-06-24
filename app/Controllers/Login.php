<?php

namespace App\Controllers;
use App\Models\Usuarios_model;

class Login extends BaseController {

    public function index() {
        $session = session();
        return view('login_view.php');
    }

    public function autenticar() {

        $usuarios_model = new Usuarios_model();

        if($this->request->getPost('usuario') and $this->request->getPost('senha')) {

            $usuario = $usuarios_model->where('usuario', $this->request->getPost('usuario'))
                                      ->where('senha', sha1($this->request->getPost('senha')))->first();

            if($usuario){
                session()->logado = 'true';
                return redirect()->to(base_url('/home'));
            }
            else {
                session()->setFlashdata('usuario', $this->request->getPost('usuario'));
                session()->setFlashdata('erro_login', 'Usuário e/ou senha inválidos!');
                return redirect()->to(base_url('/login'));
            }
        }
        else {
            return redirect()->to(base_url('/login'));
        }

    }

    public function encerrar() {
        session()->remove('logado');
        return redirect()->to(base_url('/login'));
    }
}