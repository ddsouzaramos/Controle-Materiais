<?php

namespace App\Models;
use CodeIgniter\Model;

class Fornos_model extends Model
{
    protected $table = 'fornos';
    protected $primaryKey = 'id_forno';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['descricao'];
}