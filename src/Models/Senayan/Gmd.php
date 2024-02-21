<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class Gmd extends Base
{
    protected $table = 'mst_gmd';
    protected $primaryKey = 'gmd_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
    }
}