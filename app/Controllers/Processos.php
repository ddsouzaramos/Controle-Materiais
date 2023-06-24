<?php

namespace App\Controllers;
use App\Models\Entradas_model;
use App\Models\Processos_model;
use App\Models\Processos_itens_model;
use \Hermawan\DataTables\DataTable;

class Processos extends BaseController {

    #########################
    public function index() {
    #########################
        return view('processos_view.php');
    }

    ##########################
    public function listar() {
    ##########################

        $db = db_connect();
        $builder = $db->table('processos')
                      ->join('fornos', 'fornos.id_forno = processos.id_forno')
                      ->select('processos.id_processo,
                                date_format(processos.data_processo, \'%d/%m/%Y\') data_processo,
                                fornos.descricao,
                                processos.peso_processado,
                                processos.peso_saida');

        return DataTable::of($builder)
            ->add('action', function($row) {
                                return '<button type="button" class="btn btn-danger" style="margin-top:4px" onclick="remover_processo(\'' . base_url('processos/remover') . '\', ' . $row->id_processo . ')" ><i class="fas fa-trash-alt"></i></button>' . ' ' .
                                                                          '<button type="button" class="btn btn-primary" style="margin-top:4px" onclick="editar_processo(' . $row->id_processo . ')" ><i class="fas fa-edit"></i></button>' . ' ' .
                                                                          '<button type="button" class="btn btn-dark" style="margin-top:4px" onclick="window.location.href=\'' . base_url('processos/processo_itens') . '/' . $row->id_processo . '\'" ><i class="fas fa-cart-plus"></i></button>' . ' ' .
                                                                          '<button type="button" class="btn btn-warning" style="margin-top:4px" onclick="informar_peso_saida(' . $row->id_processo . ')" ><i class="fas fa-check"></i></button>';
                            }, 'last')
            ->filter(function ($builder, $request) {
                if ($request->id_processo_filtro) {
                    $builder->where('processos.id_processo', $request->id_processo_filtro);
                }
                if ($request->data_processo_de_filtro) {
                    $builder->where('processos.data_processo >= ', implode('-', array_reverse(explode('/', $request->data_processo_de_filtro))));
                }
                if ($request->data_processo_ate_filtro) {
                    $builder->where('processos.data_processo <= ', implode('-', array_reverse(explode('/', $request->data_processo_ate_filtro))));
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

        $processos_model = new Processos_model();
        $validation =  \Config\Services::validation();

        if(!empty($this->request->getVar('id'))) {

            $id = $this->request->getVar('id');

            $validation->setRules([
                'data_processo'     => ['label' => 'Data do Processo', 'rules' => 'required'],
                'id_forno' => ['label' => 'Forno', 'rules' => 'required'],
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {

                $data = [
                    'data_processo' => implode("-", array_reverse(explode("/", $this->request->getVar('data_processo')))),
                    'id_forno' => $this->request->getVar('id_forno')
                ];

                if($processos_model->update($id, $data)) {
                    $retorno['status'] = true;
                    $retorno['mensagem'] = 'Dados atualizados com sucesso';
                    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
                }
            }
        }
        else {
            $validation->setRules([
                'data_processo'     => ['label' => 'Data do Processo', 'rules' => 'required'],
                'id_forno' => ['label' => 'Forno', 'rules' => 'required']
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $retorno['mensagem'] = 'Erro na Validação';
                $retorno['erros_validacao'] = $validation->getErrors();
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
            else {

                $data = [
                    'data_processo' => implode("-", array_reverse(explode("/", $this->request->getVar('data_processo')))),
                    'id_forno' => $this->request->getVar('id_forno'),
                    'peso_processado' => 0
                ];

                if($processos_model->insert($data)) {
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
        $processos_model = new Processos_model();
        $data = $processos_model->select('id_processo,
                                          data_processo,
                                          id_forno')
                                ->where('id_processo', $id)->first();
        echo json_encode($data);
    }

    ##############################
    public function remover($id) {
    ##############################
        $processos_model = new Processos_model();

        if($processos_model->where('id_processo', $id)->delete($id))
            echo json_encode(array("status" => true), JSON_UNESCAPED_UNICODE);
        else
            echo json_encode(array("status" => false), JSON_UNESCAPED_UNICODE);
    }

    #####################################
    public function processo_itens($id) {
    #####################################
        $processos_model = new Processos_model();
        $data = $processos_model->select('id_processo,
                                          date_format(data_processo, "%d/%m/%Y") data_processo,
                                          id_forno')
                                ->where('id_processo', $id)->first();

        return view('processos_item_view.php', $data);
    }

    ############################################
    public function listar_itens($id_processo) {
    ############################################

        $db = db_connect();
        $builder = $db->table('processos_itens')
                      ->join('entradas', 'entradas.id_entrada = processos_itens.id_entrada')
                      ->join('fornecedores', 'fornecedores.id_fornecedor = entradas.id_fornecedor')
                      ->join('tipos_materiais', 'tipos_materiais.id_tipo_material = entradas.id_tipo_material')
                      ->select('processos_itens.id_processo_item,
                                processos_itens.id_entrada,
                                entradas.id_entrada_origem,
                                date_format(entradas.data_entrada, "%d/%m/%Y")
                                data_entrada, fornecedores.nome,
                                tipos_materiais.descricao,
                                processos_itens.peso_utilizado')
                      ->where('id_processo', $id_processo);         ;

        return DataTable::of($builder)
            ->add('action', function($row) {
                                return '<button type="button" class="btn btn-danger" style="margin-top:4px" onclick="remove(\'' . base_url('processos/remover_item') . '\', ' . $row->id_processo_item . ')" ><i class="fas fa-trash-alt"></i></button>' . ' ';
                            }, 'last')
            ->toJson();
    }

    ####################################
    public function remover_item($id) {
    ####################################
        $entradas_model = new Entradas_model();
        $processos_model = new Processos_model();
        $processos_itens_model = new Processos_itens_model();

        if($processos_itens_model->where('id_processo_item', $id)->delete($id)) {

            //CALCULA PESO PROCESSADO NO PROCESSO
            $processos_model->calcular_peso_processado($this->request->getVar('id'));

            //CALCULA PESOS NA ENTRADA
            $entradas_model->calcular_peso_entrada($this->request->getVar('id_entrada'));

            echo json_encode(array("status" => true), JSON_UNESCAPED_UNICODE);
        }
        else {
            echo json_encode(array("status" => false), JSON_UNESCAPED_UNICODE);
        }
    }

    ###############################################
    public function listar_entradas_disponiveis() {
    ###############################################

        $db = db_connect();
        $builder = $db->table('entradas')
                        ->join('fornecedores', 'fornecedores.id_fornecedor = entradas.id_fornecedor')
                        ->join('tipos_materiais', 'tipos_materiais.id_tipo_material = entradas.id_tipo_material')
                        ->select('"" as checkbox,
                                  entradas.id_entrada,
                                  entradas.id_entrada_origem,
                                  date_format(entradas.data_entrada, "%d/%m/%Y") data_entrada,
                                  fornecedores.nome,
                                  tipos_materiais.descricao,
                                  entradas.peso_disponivel')
                        ->where('entradas.peso_disponivel > 0');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                if ($request->id_entrada_filtro) {
                    $builder->where('entradas.id_entrada', $request->id_entrada_filtro);
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

    ###############################
    public function salvar_item() {
    ###############################

        $retorno = array('status' => false,
                         'mensagem' => '',
                         'erros_validacao' => array()
                        );

        $entradas_model = new Entradas_model();
        $processos_model = new Processos_model();
        $processos_itens_model = new Processos_itens_model();
        $validation =  \Config\Services::validation();

        $validation->setRules([
            'id_processo' => ['label' => 'Código do processo', 'rules' => 'required'],
            'id_entrada' => ['label' => 'Código da entrada', 'rules' => 'required'],
            'peso_utilizado' => ['label' => 'Peso utilizado', 'rules' => 'required']
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $retorno['mensagem'] = 'Erro na Validação';
            $retorno['erros_validacao'] = $validation->getErrors();
            echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
        }
        else {

            $data = [
                'id_processo' => $this->request->getVar('id_processo'),
                'id_entrada' => $this->request->getVar('id_entrada'),
                'peso_utilizado' => $this->request->getVar('peso_utilizado')
            ];

            if($processos_itens_model->insert($data)) {
                //CALCULA PESO PROCESSADO NO PROCESSO
                $processos_model->calcular_peso_processado($this->request->getVar('id_processo'));

                //CALCULA PESOS NA ENTRADA
                $entradas_model->calcular_peso_entrada($this->request->getVar('id_entrada'));

                $retorno['status'] = true;
                $retorno['mensagem'] = 'Dados gravados com sucesso';
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
        }
    }

    ###############################
    public function peso_saida() {
    ###############################

        $retorno = array('status' => false,
                         'mensagem' => '',
                         'erros_validacao' => array()
                        );

        $processos_model = new Processos_model();
        $validation =  \Config\Services::validation();

        $validation->setRules([
            'id_processo' => ['label' => 'Código do processo', 'rules' => 'required'],
            'peso_saida' => ['label' => 'Peso da saída', 'rules' => 'required']
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $retorno['mensagem'] = 'Erro na Validação';
            $retorno['erros_validacao'] = $validation->getErrors();
            echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
        }
        else {

            $id_processo = $this->request->getVar('id_processo');

            $data = [
                'peso_saida' => $this->request->getVar('peso_saida')
            ];

            log_message('error', print_r($data, 1));

            if($processos_model->update($id_processo, $data)) {
                $retorno['status'] = true;
                $retorno['mensagem'] = 'Peso da saída informado com sucesso!';
                echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            }
        }
    }

}