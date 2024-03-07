<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class Member extends Base
{
    protected $table = 'member';
    protected $primaryKey = 'member_id';
    const CREATED_AT = 'input_date';
    const UPDATED_AT = 'last_update';

    public function toManyById(array $ids = [])
    {

    }
}