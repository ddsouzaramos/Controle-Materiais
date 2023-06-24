<?php

namespace App\Models;
use CodeIgniter\Model;

//////////////////////////////////////////
class Processos_itens_model extends Model
//////////////////////////////////////////
{
    protected $table = 'processos_itens';
    protected $primaryKey = 'id_processo_item';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['id_processo_item', 'id_processo', 'id_entrada', 'peso_utilizado'];
}