<?php
namespace SLiMS\Merad\Models\Inlis;

use SLiMS\Merad\Models\Base;

class Collectionloanitem extends Base
{
    protected $connection = 'inlis';
    protected $primaryKey = 'ID';
    
    public function toManyById(array $ids = [])
    {

    }
}