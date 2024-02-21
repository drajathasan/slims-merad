<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class Colltype extends Base
{
    protected $table = 'mst_coll_type';
    protected $primaryKey = 'coll_type_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
    }
}