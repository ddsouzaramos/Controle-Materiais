<?php

namespace App\Models;
use CodeIgniter\Model;

//////////////////////////////////
class Entradas_model extends Model
//////////////////////////////////
{
    protected $table = 'entradas';
    protected $primaryKey = 'id_entrada';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['data_entrada', 'id_fornecedor', 'id_tipo_material', 'peso_bruto', 'peso_limpo', 'peso_disponivel', 'status', 'id_entrada_origem', 'sequencia'];

    #####################################################
    public function calcular_peso_entrada($id_entrada) {
    #####################################################

        $sql = 'update entradas set entradas.peso_bruto = (select coalesce(sum(p.peso_bruto), 0)
                                                             from pesagens p
                                                            where p.id_entrada = entradas.id_entrada),
                                    entradas.peso_limpo = (select coalesce(sum(p.peso_bruto), 0)
                                                             from pesagens p
                                                            where p.id_entrada = entradas.id_entrada)
                                                           -
                                                          (select coalesce(sum(s.peso_bruto), 0)
                                                             from separacao s
                                                            where s.id_entrada_origem = entradas.id_entrada),
                                    entradas.peso_disponivel = (select coalesce(sum(p.peso_bruto), 0)
                                                                  from pesagens p
                                                                  where p.id_entrada = entradas.id_entrada)
                                                                -
                                                               (select coalesce(sum(s.peso_bruto), 0)
                                                                  from separacao s
                                                                 where s.id_entrada_origem = entradas.id_entrada)
                                                                -
                                                               (select coalesce(sum(i.peso_utilizado), 0)
                                                                  from processos_itens i
                                                                 where i.id_entrada = entradas.id_entrada)
                 where entradas.id_entrada = ' . $id_entrada . ';';

        $db = db_connect();
        return $db->simpleQuery($sql);
    }
}