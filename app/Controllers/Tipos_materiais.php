<?php

namespace App\Controllers;
use App\Models\Tipos_materiais_model;
use \Hermawan\DataTables\DataTable;

class Tipos_materiais extends BaseController {

    public function index() {
        return view('tipos_materiais_view.php');
    }

    public function listar() {

        $db = db_connect();
        $builder = $db->table('tipos_materiais')->select('id_tipo_material, descricao');

        return DataTable::of($builder)
            ->add('action', function($row) {
                                return '<button type="button" class="btn btn btn-secondary" onclick="edit(' . $row->id_tipo_material . ')" ><i class="fas fa-edit"></i></button>' . ' ' .
                                       '<button type="button" class="btn btn-danger" onclick="remove(\'' . base_url('tipos_materiais/remover') . '\', ' . $row->id_tipo_material . ')" ><i class="fas fa-trash-alt"></i></button>';
                            }, 'last')
            ->toJson();
    }

    public function remover($id) {
        $tipos_materiais_model = new Tipos_materiais_model();

        if($tipos_materiais_model->where('id_tipo_material', $id)->delete($id))
            echo json_encode(array("status" => true), JSON_UNESCAPED_UNICODE);
        else
            echo json_encode(array("status" => false), JSON_UNESCAPED_UNICODE);
    }

    public function salvar() {

        $retorno = array('status' => false,
                         'mensagem' => '',
                         'erros_validacao' => array());

        $tipos_materiais_model = new Tipos_materiais_model();
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

                if($tipos_materiais_model->update($id, $data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados atualizados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
        else {
            $validation->setRules([
                'descricao'     => ['label' => 'Descrição', 'rules' => 'required|min_length[5]|max_length[30]|is_unique[tipos_materiais.descricao]']
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

                if($tipos_materiais_model->insert($data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados gravados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
    }

    public function editar($id) {
        $tipos_materiais_model = new Tipos_materiais_model();
        $data = $tipos_materiais_model->where('id_tipo_material', $id)->first();
        echo json_encode($data);
    }

    public function listar_select() {
        $tipos_materiais_model = new Tipos_materiais_model();
        $data = $tipos_materiais_model->select('id_tipo_material, descricao')->findAll();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}