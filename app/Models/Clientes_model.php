<?php

namespace App\Models;
use CodeIgniter\Model;

class Clientes_model extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['nome'];
}