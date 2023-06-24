<?php

namespace App\Models;
use CodeIgniter\Model;

class Pesagens_model extends Model
{
    protected $table = 'pesagens';
    protected $primaryKey = 'id_pesagem';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['id_entrada', 'sequencia', 'peso_bruto'];
}