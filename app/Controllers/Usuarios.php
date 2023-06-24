<?php

namespace App\Controllers;
use App\Models\Usuarios_model;
use \Hermawan\DataTables\DataTable;

class Usuarios extends BaseController {

    public function index() {
        return view('usuarios_view.php');
    }

    public function listar() {

        $db = db_connect();
        $builder = $db->table('usuarios')->select('id_usuario, nome_completo, usuario');

        return DataTable::of($builder)
            ->add('action', function($row){
                                return '<button type="button" class="btn btn btn-secondary" onclick="edit(' . $row->id_usuario . ')" ><i class="fas fa-edit"></i></button>' . ' ' .
                                       '<button type="button" class="btn btn-danger" onclick="remove(\'' . base_url('usuarios/remover') . '\', ' . $row->id_usuario . ')" ><i class="fas fa-trash-alt"></i></button>';
                            }, 'last')
            ->toJson();
    }

    public function remover($id) {
        $usuarios_model = new Usuarios_model();

        if($usuarios_model->where('id_usuario', $id)->delete($id))
            echo json_encode(array("status" => true), JSON_UNESCAPED_UNICODE);
        else
            echo json_encode(array("status" => false), JSON_UNESCAPED_UNICODE);
    }

    public function salvar() {

        $retorno = array('status' => false,
                         'mensagem' => '',
                         'erros_validacao' => array());

        $usuarios_model = new Usuarios_model();
        $validation =  \Config\Services::validation();

        if(!empty($this->request->getVar('id'))) {

            $id = $this->request->getVar('id');

            // REDEFINE A SENHA
            if($this->request->getVar('alterar_senha') == 'on') {

                $validation->setRules([
                    'nome_completo'     => ['label' => 'Nome Completo',        'rules' => 'required|min_length[5]|max_length[60]'],
                    'senha'             => ['label' => 'Senha',                'rules' => 'required|min_length[6]|max_length[16]'],
                    'confirmacao_senha' => ['label' => 'Confirmação de Senha', 'rules' => 'required|min_length[6]|max_length[16]|matches[senha]']
                ]);

                if (!$validation->withRequest($this->request)->run()) {
                    $retorno['mensagem'] = 'Erro na Validação';
                    $retorno['erros_validacao'] = $validation->getErrors();
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
                else {

                    $data = [
                        'nome_completo' => $this->request->getVar('nome_completo'),
                        'senha'  => sha1($this->request->getVar('senha'))
                    ];

                    if($usuarios_model->update($id, $data)) {
                        $retorno['status'] = true;
                        $retorno['mensagem'] = 'Dados atualizados com sucesso';
                        echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                    }
                }
            }
            // ALTERAR OS DADOS SEM REDEFINIR A SENHA
            else {

                $validation->setRules([
                    'nome_completo'     => ['label' => 'Nome Completo',        'rules' => 'required|min_length[5]|max_length[60]']
                ]);

                if (!$validation->withRequest($this->request)->run()) {
                    $retorno['mensagem'] = 'Erro na Validação';
                    $retorno['erros_validacao'] = $validation->getErrors();
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
                else {

                    $data = [
                        'nome_completo' => $this->request->getVar('nome_completo'),
                        'usuario'  => $this->request->getVar('usuario')
                    ];

                    if($usuarios_model->update($id, $data)) {
                        $retorno['status'] = true;
                        $retorno['mensagem'] = 'Dados atualizados com sucesso';
                        echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                    }
                }
            }
        }

        // NOVO USUÁRIO
        else {

            $validation->setRules([
                'nome_completo'     => ['label' => 'Nome Completo',        'rules' => 'required|min_length[5]|max_length[60]'],
                'usuario'           => ['label' => 'Usuário',              'rules' => 'required|min_length[5]|max_length[60]|is_unique[usuarios.usuario]'],
                'senha'             => ['label' => 'Senha',                'rules' => 'required|min_length[6]|max_length[16]'],
                'confirmacao_senha' => ['label' => 'Confirmação de Senha', 'rules' => 'required|min_length[6]|max_length[16]|matches[senha]']
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {

                $data = [
                    'nome_completo' => $this->request->getVar('nome_completo'),
                    'usuario'  => $this->request->getVar('usuario'),
                    'senha'  => sha1($this->request->getVar('senha'))
                ];

                if($usuarios_model->insert($data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Usuário gravado com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
    }

    public function editar($id) {
        $usuarios_model = new Usuarios_model();
        $data = $usuarios_model->where('id_usuario', $id)->first();
        echo json_encode($data);
    }

}