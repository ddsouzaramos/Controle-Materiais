<?php

namespace App\Controllers;
use App\Models\Entradas_model;
use \Hermawan\DataTables\DataTable;

class Entradas extends BaseController {

    #########################
    public function index() {
    #########################
        return view('entradas_view.php');
    }

    ##########################
    public function listar() {
    ##########################

        $db = db_connect();

        $builder = $db->table('entradas')
                      ->join('fornecedores', 'fornecedores.id_fornecedor = entradas.id_fornecedor')
                      ->join('tipos_materiais', 'tipos_materiais.id_tipo_material = entradas.id_tipo_material')
                      ->select('entradas.id_entrada,
                                concat("Entrada: ", entradas.id_entrada_origem, " - Fornecedor: ", coalesce(fornecedores.nome, ""), " - Dt.Entrada: ", date_format(entradas.data_entrada, \'%d/%m/%Y\')) as id_entrada_origem,
                                concat(entradas.id_entrada_origem, case when coalesce(entradas.sequencia, 0) <> "0" then concat(".", entradas.sequencia) else "" end) as id_entrada_origem_sequencia,
                                date_format(entradas.data_entrada, \'%d/%m/%Y\') data_entrada,
                                fornecedores.nome,
                                tipos_materiais.descricao,
                                entradas.peso_bruto,
                                entradas.peso_limpo,
                                entradas.peso_disponivel');

        return DataTable::of($builder)
            ->add('action', function($row) {
                                return ($row->id_entrada_origem == null ? '<button type="button" class="btn btn-danger"  style="margin-top:4px" title="Excluir" onclick="remove(\'' . base_url('entradas/remover') . '\', ' . $row->id_entrada . ')" ><i class="fas fa-trash-alt"></i></button>' . ' ' : ' ') .
                                                                          '<button type="button" class="btn btn-primary" style="margin-top:4px" title="Editar" onclick="editar_entrada(' . $row->id_entrada . ')" ><i class="fas fa-edit"></i></button>' . ' ' .
                                                                          '<button type="button" class="btn btn-dark"    style="margin-top:4px" title="Pesar Materiais" onclick="window.location.href=\'' . base_url('pesagens/pesar') . '/' . $row->id_entrada . '\'" ><i class="fas fa-balance-scale-left"></i></button>' . ' ' .
                                                                          '<button type="button" class="btn btn-warning" style="margin-top:4px" title="Separar Materiais" onclick="window.location.href=\'' . base_url('separacao/separar') . '/' . $row->id_entrada . '\'" ><i class="fas fa-exchange-alt"></i></button>';
                            }, 'last')
            ->filter(function ($builder, $request) {
                if ($request->exibir_saldo_zero != 'true') {
                    $builder->where('(entradas.peso_disponivel > 0 or (entradas.peso_disponivel = 0 and entradas.peso_bruto = 0))');
                }
                if ($request->id_entrada_filtro) {
                    $builder->where('entradas.id_entrada', $request->id_entrada_filtro);
                }
                if ($request->data_entrada_de_filtro) {
                    $builder->where('entradas.data_entrada >= ', implode('-', array_reverse(explode('/', $request->data_entrada_de_filtro))));
                }
                if ($request->data_entrada_ate_filtro) {
                    $builder->where('entradas.data_entrada <= ', implode('-', array_reverse(explode('/', $request->data_entrada_ate_filtro))));
                }
                if ($request->id_fornecedor_filtro) {
                    $builder->where('entradas.id_fornecedor', $request->id_fornecedor_filtro);
                }
                if ($request->id_tipo_material_filtro) {
                    $builder->where('entradas.id_tipo_material', $request->id_tipo_material_filtro);
                }
            }
            )
            ->toJson();
    }

    ##########################
    public function salvar() {
    ##########################

        $retorno = array('status' => false,
                         'mensagem' => '',
                         'erros_validacao' => array());

        $entradas_model = new Entradas_model();
        $validation =  \Config\Services::validation();

        if(!empty($this->request->getVar('id'))) {

            $id = $this->request->getVar('id');

            $validation->setRules([
                'data_entrada'     => ['label' => 'Data da Entrada', 'rules' => 'required'],
                'id_fornecedor' => ['label' => 'Fornecedor', 'rules' => 'required'],
                'id_tipo_material' => ['label' => 'Tipo Material', 'rules' => 'required']
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {

                $data = [
                    'data_entrada' => $this->request->getVar('data_entrada'),
                    'id_fornecedor' => $this->request->getVar('id_fornecedor'),
                    'id_tipo_material' => $this->request->getVar('id_tipo_material')
                ];

                if($entradas_model->update($id, $data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados atualizados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
        else {
            $validation->setRules([
                'data_entrada'     => ['label' => 'Data da Entrada', 'rules' => 'required'],
                'id_fornecedor' => ['label' => 'Fornecedor', 'rules' => 'required'],
                'id_tipo_material' => ['label' => 'Tipo Material', 'rules' => 'required']
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {

                $data = [
                    'data_entrada' => implode("-", array_reverse(explode("/", $this->request->getVar('data_entrada')))),
                    'id_fornecedor' => $this->request->getVar('id_fornecedor'),
                    'id_tipo_material' => $this->request->getVar('id_tipo_material'),
                    'peso_bruto' => 0,
                    'peso_limpo' => 0,
                    'peso_disponivel' => 0,
                    'status' => 'A',
                    'id_entrada_origem' => null,
                    'sequencia' => null
                ];

                if($entradas_model->insert($data)) {

                    //Atualiza o id_entrada_origem
                    $id_entrada_insert = $entradas_model->getInsertID();
                    $entradas_model->update($id_entrada_insert, ['id_entrada_origem' => $id_entrada_insert]);

                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados gravados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
    }

    #############################
    public function editar($id) {
    #############################
        $entradas_model = new Entradas_model();
        $data = $entradas_model->select('id_entrada,
                                         data_entrada,
                                         id_fornecedor,
                                         id_tipo_material')
                               ->where('id_entrada', $id)->first();
        echo json_encode($data);
    }

    ##############################
    public function remover($id) {
    ##############################
        $entradas_model = new Entradas_model();

        if($entradas_model->where('id_entrada', $id)->delete($id))
            echo json_encode(array("status" => true), JSON_UNESCAPED_UNICODE);
        else
            echo json_encode(array("status" => false), JSON_UNESCAPED_UNICODE);
    }

    ##########################################
    public function buscar_peso_entrada($id) {
    ##########################################
        $entradas_model = new Entradas_model();

        $peso = $entradas_model->select('peso_bruto, peso_limpo, peso_disponivel')
                               ->where('id_entrada', $id)
                               ->get()
                               ->getRow();

        echo json_encode(array("peso_bruto" => $peso->peso_bruto,
                               "peso_limpo" => $peso->peso_limpo,
                               "peso_disponivel" => $peso->peso_disponivel), JSON_UNESCAPED_UNICODE);
    }

}