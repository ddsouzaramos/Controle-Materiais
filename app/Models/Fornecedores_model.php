<?php

namespace App\Models;
use CodeIgniter\Model;

class Fornecedores_model extends Model
{
    protected $table = 'fornecedores';
    protected $primaryKey = 'id_fornecedor';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['nome'];
}