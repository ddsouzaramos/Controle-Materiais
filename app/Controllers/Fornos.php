<?php

namespace App\Controllers;
use App\Models\Fornos_model;
use \Hermawan\DataTables\DataTable;

class Fornos extends BaseController {

    public function index() {
        return view('fornos_view.php');
    }

    public function listar() {

        $db = db_connect();
        $builder = $db->table('fornos')->select('id_forno, descricao');

        return DataTable::of($builder)
            ->add('action', function($row) {
                                return '<button type="button" class="btn btn btn-secondary" onclick="edit(' . $row->id_forno . ')" ><i class="fas fa-edit"></i></button>' . ' ' .
                                       '<button type="button" class="btn btn-danger" onclick="remove(\'' . base_url('fornos/remover') . '\', ' . $row->id_forno . ')" ><i class="fas fa-trash-alt"></i></button>';
                            }, 'last')
            ->toJson();
    }

    public function remover($id) {
        $fornos_model = new Fornos_model();

        if($fornos_model->where('id_forno', $id)->delete($id))
            echo json_encode(array("status" => true), JSON_UNESCAPED_UNICODE);
        else
            echo json_encode(array("status" => false), JSON_UNESCAPED_UNICODE);
    }

    public function salvar() {

        $retorno = array('status' => false,
                         'mensagem' => '',
                         'erros_validacao' => array());

        $fornos_model = new Fornos_model();
        $validation =  \Config\Services::validation();

        if(!empty($this->request->getVar('id'))) {

            $id = $this->request->getVar('id');

            $validation->setRules([
                'descricao'     => ['label' => 'Descrição', 'rules' => 'required|min_length[5]|max_length[30]']
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {

                $data = [
                    'descricao' => $this->request->getVar('descricao')
                ];

                if($fornos_model->update($id, $data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados atualizados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
        else {
            $validation->setRules([
                'descricao'     => ['label' => 'Descrição', 'rules' => 'required|min_length[5]|max_length[30]|is_unique[fornos.descricao]']
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {

                $data = [
                    'descricao' => $this->request->getVar('descricao')
                ];

                if($fornos_model->insert($data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados gravados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
    }

    public function editar($id) {
        $fornos_model = new Fornos_model();
        $data = $fornos_model->where('id_forno', $id)->first();
        echo json_encode($data);
    }

    public function listar_select() {
        $fornos_model = new Fornos_model();
        $data = $fornos_model->select('id_forno, descricao')->findAll();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function listar_fornos_ativos() {
        $fornos_model = new Fornos_model();

        $sql = "select fornos.id_forno,
                       fornos.descricao
                  from fornos";

        $query = $fornos_model->query($sql);
        $data = $query->getResultArray();

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}