<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;

class BiblioAuthor extends Base
{
    protected $table = 'biblio_author';
    protected $primaryKey = 'biblio_id';
    public $timestamps = false;
    protected $fillable = ['author_id'];
    
    // public function boot(){
     
    //     BiblioAuthor::unguard();
    // }

    public function scopeGetFormattedAuthor($query, $biblio_id)
    {
        $authors = $query
                    ->select('biblio_author.biblio_id', 'biblio_author.level', 'au.author_name AS name', 'au.authority_type AS `type`')
                    ->join('mst_author as au', 'biblio_author.author_id', '=', 'au.author_id', 'left')
                    ->where('biblio_author.biblio_id', $biblio_id);

        $author_string = [];
        foreach ($authors->get() as $seq => $author) {
            $author_string[] = $author->name;
        }

        return implode('-', $author_string);
    }

    public function toManyById(array $ids = [])
    {
        
    }
}