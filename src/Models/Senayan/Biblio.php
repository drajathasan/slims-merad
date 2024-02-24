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
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function scopeMakeFullIndex($query)
    {
        SearchBiblio::truncate();
        foreach ($this->cursor() as $biblio) {
            $this->makeIndex($biblio->biblio_id);
        }
    }

    public function scopeMakeIndex($query, int $biblio_id)
    {
        $query->select('biblio.biblio_id','biblio.title','biblio.edition','biblio.publish_year','biblio.notes','biblio.series_title','biblio.classification','biblio.spec_detail_info',
            'g.gmd_name AS gmd', 'pb.publisher_name AS publisher', 'pl.place_name AS publish_place','biblio.isbn_issn',
            'lg.language_name AS language','biblio.call_number','biblio.opac_hide','biblio.promoted','biblio.labels','biblio.collation','biblio.image',
	    'rct.content_type', 'rmt.media_type', 'rcrt.carrier_type',
	    'biblio.input_date','biblio.last_update');
        $query
            ->join('mst_gmd AS g', 'biblio.gmd_id', '=', 'g.gmd_id', 'left')
            ->join('mst_publisher AS pb', 'biblio.publisher_id', '=', 'pb.publisher_id', 'left')
            ->join('mst_place AS pl', 'biblio.publish_place_id', '=', 'pl.place_id', 'left')
            ->join('mst_language AS lg', 'biblio.language_id', '=', 'lg.language_id', 'left')
            ->join('mst_content_type AS rct', 'biblio.content_type_id', '=', 'lg.language_id', 'left')
            ->join('mst_media_type AS rmt', 'biblio.media_type_id', '=', 'rmt.id', 'left')
            ->join('mst_carrier_type AS rcrt', 'biblio.carrier_type_id', '=', 'rcrt.id', 'left');

        $query->where('biblio.biblio_id', $biblio_id);

        $biblio = $query->first();

        if ($biblio->content_type === null) $biblio->content_type = '';
        if ($biblio->media_type === null) $biblio->media_type = '';
        if ($biblio->carrier_type === null) $biblio->carrier_type = '';
        $biblio->publish_place = substr($biblio->publish_place, 0,30);

        if (!empty($biblio->notes)) 
            $biblio->notes = strip_tags($biblio->notes??'', '<br><p><div><span><i><em><strong><b><code>');

        $biblio->author = BiblioAuthor::getFormattedAuthor($biblio_id);

        $biblio->topic = BiblioTopic::getFormattedTopic($biblio_id);

        $biblio->items = Item::getFormattedItem($biblio_id);

        return SearchBiblio::insert($biblio->toArray());
    }

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

    public function toArray()
    {
        return $this->attributes;
    }
}