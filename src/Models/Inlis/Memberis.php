<?php
namespace SLiMS\Merad\Models\Inlis;

use SLiMS\Merad\Models\Base;

class Memberis extends Base
{
    protected $table = 'members';
    protected $primaryKey = 'ID';
    protected $connection = 'inlis';

    public function toManyById(array $ids = [])
    {

    }
}