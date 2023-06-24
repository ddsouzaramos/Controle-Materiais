<?php

namespace App\Controllers;
use App\Models\Entradas_model;
use App\Models\Pesagens_model;
use \Hermawan\DataTables\DataTable;

class Pesagens extends BaseController {

    ####################################
    public function pesar($id_entrada) {
    ####################################
        $entradas_model = new Entradas_model();
        $data = $entradas_model->select('id_entrada,
                                         date_format(data_entrada, "%d/%m/%Y") as data_entrada,
                                         fornecedores.id_fornecedor,
                                         fornecedores.nome,
                                         tipos_materiais.descricao,
                                         entradas.peso_bruto')
                               ->join('fornecedores', 'fornecedores.id_fornecedor = entradas.id_fornecedor')
                               ->join('tipos_materiais', 'tipos_materiais.id_tipo_material = entradas.id_tipo_material')
                               ->where('id_entrada', $id_entrada)->first();
        return view('pesagens_view.php', $data);
    }

    #####################################
    public function listar($id_entrada) {
    #####################################
        $db = db_connect();
        $builder = $db->table('pesagens')
                      ->where('pesagens.id_entrada = ' . $id_entrada)
                      ->select('pesagens.id_pesagem,
                                pesagens.sequencia,
                                pesagens.peso_bruto');

        return DataTable::of($builder)
            ->add('action', function($row) {
                                return '<button type="button" class="btn btn-danger" style="margin-top:4px" onclick="remove(\'' . base_url('pesagens/remover') . '\',' . $row->id_pesagem . ')" ><i class="fas fa-trash-alt"></i></button>';
                            }, 'last')
            ->toJson();
    }

    ##########################
    public function salvar() {
    ##########################
        $entradas_model = new Entradas_model();
        $pesagens_model = new Pesagens_model();
        $validation = \Config\Services::validation();

        $retorno = array('status' => false,
                         'mensagem' => '',
                         'erros_validacao' => array());

        if(!empty($this->request->getVar('id_entrada'))) {

            $id_entrada = $this->request->getVar('id_entrada');

            $validation->setRules([
                'peso_bruto'     => ['label' => 'Peso', 'rules' => 'required']
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {
                $ultima_sequencia_entrada = $pesagens_model->select('coalesce(max(sequencia), 0) + 1 as sequencia')
                                                           ->where('id_entrada', $id_entrada)
                                                           ->get()
                                                           ->getRow()
                                                           ->sequencia;

                $data = [
                    'id_entrada' => $id_entrada,
                    'sequencia' => $ultima_sequencia_entrada,
                    'peso_bruto' => str_replace(array('.', ','), array('', '.'), $this->request->getVar('peso_bruto'))
                ];

                if($pesagens_model->insert($data)) {
                    $entradas_model->calcular_peso_entrada($id_entrada);
                    $retorno['status'] = true;
                    $retorno['mensagem'] = '';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
    }

    ##############################
    public function remover($id) {
    ##############################
        $entradas_model = new Entradas_model();
        $pesagens_model = new Pesagens_model();

        $id_entrada = $pesagens_model->select('id_entrada')
                                     ->where('id_pesagem', $id)
                                     ->get()
                                     ->getRow()
                                     ->id_entrada;

        if($pesagens_model->where('id_pesagem', $id)->delete($id)) {
            $entradas_model->calcular_peso_entrada($id_entrada);
            echo json_encode(array("status" => true), JSON_UNESCAPED_UNICODE);
        }
        else {
            echo json_encode(array("status" => false), JSON_UNESCAPED_UNICODE);
        }
    }

}