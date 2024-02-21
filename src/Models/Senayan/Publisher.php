<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class Publisher extends Base
{
    protected $table = 'mst_publisher';
    protected $primaryKey = 'publisher_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
    }
}