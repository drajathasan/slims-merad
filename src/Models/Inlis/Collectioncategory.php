<?php
namespace SLiMS\Merad\Models\Inlis;

use SLiMS\Merad\Models\Base;

class Collectioncategory extends Base
{
    protected $table = 'collectioncategorys';
    protected $connection = 'inlis';
    protected $primaryKey = 'ID';
    public function toManyById(array $ids = [])
    {

    }
}