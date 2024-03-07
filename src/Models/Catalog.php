<?php
namespace SLiMS\Merad\Models\Inlis;

use SLiMS\Merad\Models\Base;

class Catalog extends Base
{
    protected $connection = 'inlis';

    public function toManyById(array $ids = [])
    {

    }
}