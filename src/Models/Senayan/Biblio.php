<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Contract;

class Biblio extends Contract
{
    protected $table = 'biblio';
    protected $primaryKey = 'biblio_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toOther()
    {
        $lastId = $this->biblio_id;

        
    }
}