<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;
use SLiMS\Merad\Models\Inlis\Worksheet;

class SearchBiblio extends Base
{
    protected $table = 'search_biblio';
    protected $primaryKey = 'biblio_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
        
        //  
    }
}