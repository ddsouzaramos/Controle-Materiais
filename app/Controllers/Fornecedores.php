<?php

namespace App\Controllers;
use App\Models\Fornecedores_model;
use \Hermawan\DataTables\DataTable;

class Fornecedores extends BaseController {

    public function index() {
        return view('fornecedores_view.php');
    }

    public function listar() {

        $db = db_connect();
        $builder = $db->table('fornecedores')->select('id_fornecedor, nome');

        return DataTable::of($builder)
            ->add('action', function($row){
                                return '<button type="button" class="btn btn btn-secondary" onclick="edit(' . $row->id_fornecedor . ')" ><i class="fas fa-edit"></i></button>' . ' ' .
                                       '<button type="button" class="btn btn-danger" onclick="remove(\'' . base_url('fornecedores/remover') . '\', ' . $row->id_fornecedor . ')" ><i class="fas fa-trash-alt"></i></button>';
                            }, 'last')
            ->toJson();
    }

    public function remover($id) {
        $fornecedores_model = new Fornecedores_model();

        if($fornecedores_model->where('id_fornecedor', $id)->delete($id))
            echo json_encode(array("status" => true), JSON_UNESCAPED_UNICODE);
        else
            echo json_encode(array("status" => false), JSON_UNESCAPED_UNICODE);
    }

    public function salvar() {

        $retorno = array('status' => false,
                         'mensagem' => '',
                         'erros_validacao' => array());

        $fornecedores_model = new Fornecedores_model();
        $validation =  \Config\Services::validation();

        if(!empty($this->request->getVar('id'))) {

            $id = $this->request->getVar('id');

            $validation->setRules([
                'nome'     => ['label' => 'Nome Fornecedor', 'rules' => 'required|min_length[5]|max_length[60]']
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {

                $data = [
                    'nome' => $this->request->getVar('nome')
                ];

                if($fornecedores_model->update($id, $data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados atualizados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
        else {
            $validation->setRules([
                'nome'     => ['label' => 'Nome Fornecedor', 'rules' => 'required|min_length[5]|max_length[60]|is_unique[fornecedores.nome]']
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {

                $data = [
                    'nome' => $this->request->getVar('nome')
                ];

                if($fornecedores_model->insert($data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados gravados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
    }

    public function editar($id) {
        $fornecedores_model = new Fornecedores_model();
        $data = $fornecedores_model->where('id_fornecedor', $id)->first();
        echo json_encode($data);
    }

    public function listar_select() {
        $fornecedores_model = new Fornecedores_model();
        $data = $fornecedores_model->select('id_fornecedor, nome')->findAll();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}