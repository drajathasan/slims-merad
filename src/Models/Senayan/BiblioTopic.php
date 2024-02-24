<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class BiblioTopic extends Base
{
    protected $table = 'biblio_topic';
    protected $primaryKey = 'biblio_id';
    public $timestamps = false;
    protected $fillable = ['topic_id'];

    public function scopeGetFormattedTopic($query, $biblio_id)
    {
        $topics = $query
                    ->select('biblio_topic.biblio_id', 'biblio_topic.level', 'tp.topic AS topic', 'tp.topic_type')
                    ->join('mst_topic as tp', 'biblio_topic.topic_id', '=', 'tp.topic_id', 'left')
                    ->where('biblio_topic.biblio_id', $biblio_id);

        $topic_string = [];
        foreach ($topics->get() as $seq => $topic) {
            $topic_string[] = $topic->name;
        }

        return implode('-', $topic_string);
    }

    public function toManyById(array $ids = [])
    {
    }
}