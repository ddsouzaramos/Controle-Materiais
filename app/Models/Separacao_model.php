<?php

namespace App\Models;
use CodeIgniter\Model;

class Separacao_model extends Model {
    protected $table = 'separacao';
    protected $primaryKey = 'id_separacao';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['id_entrada_origem', 'data', 'id_tipo_material', 'peso_bruto', 'id_entrada_separacao'];
}