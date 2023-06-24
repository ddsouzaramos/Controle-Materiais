<?php

namespace App\Models;
use CodeIgniter\Model;

######################################
class Processos_model extends Model {
######################################
    protected $table = 'processos';
    protected $primaryKey = 'id_processo';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['id_processo', 'data_processo', 'id_forno', 'peso_processado', 'peso_saida'];

    ########################################################
    public function calcular_peso_processado($id_processo) {
    ########################################################

        $sql = 'update processos set processos.peso_processado = (select coalesce(sum(i.peso_utilizado), 0)
                                                                    from processos_itens i
                                                                    where i.id_processo = processos.id_processo)
                    where processos.id_processo = ' . $id_processo;

        $db = db_connect();
        return $db->simpleQuery($sql);
    }
}