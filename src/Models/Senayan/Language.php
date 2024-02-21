<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class Language extends Base
{
    protected $table = 'mst_language';
    protected $primaryKey = 'language_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
    }
}