<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Contract;

class BiblioAuthor extends Contract
{
    protected $table = 'biblio_author';
    protected $primaryKey = 'biblio_id';
    public $timestamps = false;
    protected $fillable = ['author_id'];
    
    // public function boot(){
     
    //     BiblioAuthor::unguard();
    // }

    public function toManyById(array $ids = [])
    {
    }
}