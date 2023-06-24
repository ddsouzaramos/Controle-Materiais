<?php

namespace App\Models;
use CodeIgniter\Model;

class Tipos_materiais_model extends Model
{
    protected $table = 'tipos_materiais';
    protected $primaryKey = 'id_tipo_material';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['descricao'];
}