<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class Subject extends Base
{
    protected $table = 'mst_topic';
    protected $primaryKey = 'topic_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
        BiblioTopic::insertOrIgnore($ids);
    }
}