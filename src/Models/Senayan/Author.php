<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Contract;

class Author extends Contract
{
    protected $table = 'mst_author';
    protected $primaryKey = 'author_name';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public static function createOrGetIfExists($authors, $sourceModel, $lastBiblioId)
    {
        foreach (explode(';', $authors) as $authorName) {
            $authorData = self::find($authorName);

            if ($authorData === null) {
                $authorData = new Author;
                $authorData->author_name = trim($authorName);
                $authorData->author_year = date('Y');
                $authorData->save();
            }

            $authorData->toManyById([
                'author_id' => $authorData->author_id,
                'biblio_id' => $lastBiblioId
            ]);
        }
    }

    public function toManyById(array $ids = [])
    {
        BiblioAuthor::insert($ids);
    }
}