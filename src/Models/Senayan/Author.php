<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class Author extends Base
{
    protected $table = 'mst_author';
    protected $primaryKey = 'author_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
        BiblioAuthor::insertOrIgnore($ids);
    }
}