<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;
use SLiMS\Merad\Models\Inlis\Worksheet;
use SLiMS\Merad\Models\Inlis\Collectioncategory;

class Item extends Base
{
    protected $table = 'item';
    protected $primaryKey = 'item_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
    }

    public function updateId()
    {
        $source = $this->sourceModelInstance;

        $collectionCategory = Collectioncategory::find($source->Category_id);
        
        if ($collectionCategory !== null) {
            $collectionType = Colltype::where('coll_type_name', $collectionCategory->Name)->first();

            if ($collectionType === null) {
                $collectionType = new Colltype;
                $collectionType->coll_type_name = $collectionCategory->Name;
                $collectionType->save();
            }

            $this->where('inID', $source->ID)->update([
                'coll_type_id' => $collectionType->coll_type_id
            ]);
        }

        $biblio = Biblio::where('inID', $source->Catalog_id)->first();

        if ($biblio !== null) {
            $this->where('inID', $source->ID)->update([
                'biblio_id' => $biblio->biblio_id
            ]);
        }
    }
}