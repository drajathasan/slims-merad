<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class BiblioTopic extends Base
{
    protected $table = 'biblio_topic';
    protected $primaryKey = 'biblio_id';
    public $timestamps = false;
    protected $fillable = ['topic_id'];

    public function toManyById(array $ids = [])
    {
    }
}