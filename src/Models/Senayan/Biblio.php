<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;
use SLiMS\Merad\Models\Inlis\Worksheet;

class Biblio extends Base
{
    protected $table = 'biblio';
    protected $primaryKey = 'biblio_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
        $source = $this->sourceModelInstance;

        if (isset($source->Author)) {
            Author::createOrGetIfExists($source->Author, [
                'sourceModel' => $this,
                'primaryKey' => 'author_id',
                'criteria' => 'author_name',
                'lastBiblioId' => $this->biblio_id,
                'fields' => [
                    'author_name',
                    ['author_year', date('Y')]
                ]
            ]);
        }
        
        if (isset($source->Subject)) {
            Subject::createOrGetIfExists($source->Subject, [
                'sourceModel' => $this,
                'primaryKey' => 'topic_id',
                'criteria' => 'topic',
                'lastBiblioId' => $this->biblio_id,
                'fields' => [
                    'topic',
                    ['topic_type', 't'],
                    ['classification', '']
                ]
            ]);
        }

        if (isset($source->Publisher)) {
            $publisher_id = Publisher::createOrGetIfExists($source->Publisher, [
                'sourceModel' => $this,
                'primaryKey' => 'publisher_id',
                'criteria' => 'publisher_name',
                'lastBiblioId' => $this->biblio_id,
                'fields' => [
                    'publisher_name'
                ]
            ])[0]??0;

            $this->where('biblio_id', $this->biblio_id)->update([
                'publisher_id' => $publisher_id
            ]);
        }

        if (isset($source->PublishLocation)) {
            $publish_place_id = Place::createOrGetIfExists($source->PublishLocation, [
                'sourceModel' => $this,
                'primaryKey' => 'place_id',
                'criteria' => 'place_name',
                'lastBiblioId' => $this->biblio_id,
                'fields' => [
                    'place_name'
                ]
            ])[0]??0;

            $this->where('biblio_id', $this->biblio_id)->update([
                'publish_place_id' => $publish_place_id
            ]);
        }

        if (isset($source->Languages)) {
            $language_id = Language::createOrGetIfExists($source->Languages, [
                'sourceModel' => $this,
                'primaryKey' => 'language_id',
                'criteria' => 'language_id',
                'lastBiblioId' => $this->biblio_id,
                'fields' => [
                    'language_id',
                    'language_name'
                ]
            ])[0]??'id';

            $this->where('biblio_id', $this->biblio_id)->update([
                'language_id' => $language_id
            ]);
        }

        if (isset($source->Worksheet_id)) {
            $worksheet = Worksheet::find($source->Worksheet_id);

            $gmd_id = Gmd::createOrGetIfExists($worksheet?->Name??'', [
                'sourceModel' => $this,
                'primaryKey' => 'gmd_id',
                'criteria' => 'gmd_name',
                'lastBiblioId' => $this->biblio_id,
                'fields' => [
                    'gmd_name'
                ]
            ])[0]??0;

            $this->where('biblio_id', $this->biblio_id)->update([
                'gmd_id' => $gmd_id
            ]);
        }
        //  
    }
}