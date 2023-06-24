<?php

namespace App\Controllers;
use App\Models\Clientes_model;
use \Hermawan\DataTables\DataTable;

class Clientes extends BaseController {

    public function index() {
        return view('clientes_view.php');
    }

    public function listar() {

        $db = db_connect();
        $builder = $db->table('clientes')->select('id_cliente, nome');

        return DataTable::of($builder)
            ->add('action', function($row){
                                return '<button type="button" class="btn btn btn-secondary" onclick="edit(' . $row->id_cliente . ')" ><i class="fas fa-edit"></i></button>' . ' ' .
                                       '<button type="button" class="btn btn-danger" onclick="remove(\'' . base_url('clientes/remover') . '\', ' . $row->id_cliente . ')" ><i class="fas fa-trash-alt"></i></button>';
                            }, 'last')
            ->toJson();
    }

    public function remover($id) {
        $clientes_model = new Clientes_model();

        if($clientes_model->where('id_cliente', $id)->delete($id))
            echo json_encode(array("status" => true), JSON_UNESCAPED_UNICODE);
        else
            echo json_encode(array("status" => false), JSON_UNESCAPED_UNICODE);
    }

    public function salvar() {

        $retorno = array('status' => false,
                         'mensagem' => '',
                         'erros_validacao' => array());

        $clientes_model = new Clientes_model();
        $validation =  \Config\Services::validation();

        if(!empty($this->request->getVar('id'))) {

            $id = $this->request->getVar('id');

            $validation->setRules([
                'nome'     => ['label' => 'Nome Cliente', 'rules' => 'required|min_length[5]|max_length[60]']
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

                if($clientes_model->update($id, $data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados atualizados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
        else {
            $validation->setRules([
                'nome'     => ['label' => 'Nome Cliente', 'rules' => 'required|min_length[5]|max_length[60]|is_unique[clientes.nome]']
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

                if($clientes_model->insert($data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados gravados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
    }

    public function editar($id) {
        $clientes_model = new Clientes_model();
        $data = $clientes_model->where('id_cliente', $id)->first();
        echo json_encode($data);
    }

    public function listar_select() {
        $clientes_model = new Clientes_model();
        $data = $clientes_model->select('id_cliente, nome')->findAll();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}