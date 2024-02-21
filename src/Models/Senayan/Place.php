<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class Place extends Base
{
    protected $table = 'mst_place';
    protected $primaryKey = 'place_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
    }
}