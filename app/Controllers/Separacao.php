<?php

namespace App\Controllers;
use App\Models\Entradas_model;
use App\Models\Pesagens_model;
use App\Models\Separacao_model;
use \Hermawan\DataTables\DataTable;

class Separacao extends BaseController {

    /////////////////////////////////////
    public function separar($id_entrada) {
    /////////////////////////////////////
        $entradas_model = new Entradas_model();
        $data = $entradas_model->select('id_entrada,
                                         date_format(data_entrada, "%d/%m/%Y") data_entrada,
                                         fornecedores.id_fornecedor,
                                         fornecedores.nome,
                                         tipos_materiais.descricao,
                                         entradas.peso_disponivel')
                               ->join('fornecedores', 'fornecedores.id_fornecedor = entradas.id_fornecedor')
                               ->join('tipos_materiais', 'tipos_materiais.id_tipo_material = entradas.id_tipo_material')
                               ->where('id_entrada', $id_entrada)->first();
        return view('separacao_view.php', $data);
    }

    //////////////////////////////////////
    public function listar($id_entrada) {
    //////////////////////////////////////

        $db = db_connect();
        $builder = $db->table('separacao')
                      ->where('separacao.id_entrada_origem = ' . $id_entrada)
                      ->join('tipos_materiais', 'tipos_materiais.id_tipo_material = separacao.id_tipo_material')
                      ->select('separacao.id_separacao,
                                date_format(separacao.data, "%d/%m/%Y") data,
                                tipos_materiais.descricao,
                                separacao.peso_bruto');

        return DataTable::of($builder)
            ->add('action', function($row) {
                                return '<button type="button" class="btn btn-danger" style="margin-top:4px" onclick="remove(\'' . base_url('separacao/remover') . '\', ' . $row->id_separacao . ')" ><i class="fas fa-trash-alt"></i></button>';
                            }, 'last')
            ->toJson();
    }

    ///////////////////////////
    public function salvar() {
    ///////////////////////////
        $entradas_model = new Entradas_model();
        $pesagens_model = new Pesagens_model();
        $separacao_model = new Separacao_model();
        $validation = \Config\Services::validation();

        $retorno = array('status' => false,
                         'mensagem' => '',
                         'erros_validacao' => array());

        if(!empty($this->request->getVar('id_entrada_origem'))) {

            $id_entrada_origem = $this->request->getVar('id_entrada_origem');

            $validation->setRules([
                'id_tipo_material'  => ['label' => 'Tipo Material', 'rules' => 'required'],
                'peso_bruto'     => ['label' => 'Peso', 'rules' => 'required']
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {
                $id_tipo_material = $this->request->getVar('id_tipo_material');
                $peso_bruto = str_replace(",", ".", $this->request->getVar('peso_bruto'));

                //BUSCA ENTRADA ORIGEM
                $entrada_origem = $entradas_model->where('id_entrada', $id_entrada_origem)
                                                 ->select('*')
                                                 ->first();

                //VERIFICA SE JÁ FOI SEPARADO O TIPO DE MATERIAL
                $separacao_anterior = $separacao_model->where('id_entrada_origem', $id_entrada_origem)
                                                      ->where('id_tipo_material', $id_tipo_material)
                                                      ->select('*')
                                                      ->first();

                //VERIFICA SE O MATERIAL SEPARADO É IGUAL AO MATERIAL DE ORIGEM
                if ($entrada_origem['id_tipo_material'] == $id_tipo_material) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'O material separado não pode ser igual ao material da entrada';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
                //VERIFICA SE O PESO LIMPO É MENOR QUE O PESO SEPARADO
                elseif ($entrada_origem['peso_disponivel'] < $peso_bruto) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'O peso separado é maior que o peso disponível da entrada';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
                //VERIFICA SE O MATERIAL SEPARADO JÁ EXISTE
                elseif (!empty($separacao_anterior)) {
                    $separacao_model->set('peso_bruto', $peso_bruto + $separacao_anterior['peso_bruto'])
                                    ->where('id_separacao', $separacao_anterior['id_separacao'])
                                    ->update();
                    $entradas_model->calcular_peso_entrada($id_entrada_origem);
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Peso considerado para o material separado';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
                else {
                    $data_entrada = [
                                     'data_entrada' => $entrada_origem['data_entrada'],
                                     'id_fornecedor' => $entrada_origem['id_fornecedor'],
                                     'id_tipo_material' => $id_tipo_material,
                                     'peso_bruto' => $peso_bruto,
                                     'peso_limpo' => $peso_bruto,
                                     'peso_disponivel' => $peso_bruto,
                                     'status' => 'A',
                                     'id_entrada_origem' => $entrada_origem['id_entrada']
                    ];

                    if($entradas_model->insert($data_entrada)) {

                        $ultimo_id_entrada = $entradas_model->insertID();

                        $data_pesagem = [
                                         'id_entrada' => $ultimo_id_entrada,
                                         'sequencia' => 1,
                                         'peso_bruto' => $peso_bruto
                        ];

                        if($pesagens_model->insert($data_pesagem)) {

                            $data_separacao = [
                                               'id_entrada_origem' => $id_entrada_origem,
                                               'data' =>  date('Y-m-d'),
                                               'id_tipo_material' => $id_tipo_material,
                                               'peso_bruto' => $peso_bruto,
                                               'id_entrada_separacao' => $ultimo_id_entrada
                            ];

                            if($separacao_model->insert($data_separacao)) {
                                $entradas_model->calcular_peso_entrada($id_entrada_origem);
                                $retorno['status'] = true;
                                $retorno['mensagem'] = 'Separação realizada com sucesso';
                                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }
                }

                /*
                $data = [
                    'id_entrada' => $id_entrada,
                    'peso_bruto' => $this->request->getVar('peso_bruto')
                ];

                if($separacao_model->insert($data)) {
                    $entradas_model->calcular_peso_entrada($id_entrada);
                    $retorno['status'] = true;
                    $retorno['mensagem'] = '';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
                */

            }
        }
    }

    //////////////////////////////
    public function remover($id) {
    //////////////////////////////
        $entradas_model = new Entradas_model();
        $separacao_model = new Separacao_model();

        $id_entrada_origem = $separacao_model->select('id_entrada_origem')
                                             ->where('id_separacao', $id)
                                             ->get()
                                             ->getRow()
                                             ->id_entrada_origem;

        $id_entrada_separacao = $separacao_model->select('id_entrada_separacao')
                                                ->where('id_separacao', $id)
                                                ->get()
                                                ->getRow()
                                                ->id_entrada_separacao;

        if($entradas_model->where('id_entrada', $id_entrada_separacao)->delete($id_entrada_separacao)) {
            $entradas_model->calcular_peso_entrada($id_entrada_origem);
            echo json_encode(array("status" => true), JSON_UNESCAPED_UNICODE);
        }
        else {
            echo json_encode(array("status" => false), JSON_UNESCAPED_UNICODE);
        }
    }

}