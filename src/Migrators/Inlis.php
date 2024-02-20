<?php
namespace SLiMS\Merad\Migrators;

use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;
use SLiMS\Merad\Models\Inlis\Catalog;

class Inlis extends Contract
{
    public function migrate()
    {
        $this->catalogToBiblio();
    }

    private function catalogToBiblio()
    {
        if (!Schema::hasColumn('biblio', 'inID')) {
            Schema::table('biblio', function(Blueprint $table) {
                $table->string('isbn_issn', 700)->nullable()->change();
                $table->number('inID', 11)->nullable()->add();
                $table->index('inID')->add();
            });

            Schema::table('search_biblio', function(Blueprint $table) {
                $table->string('isbn_issn', 700)->nullable()->change();
            });
        }

        $map = [
            'ID' => 'inID',
            'Title' => ['title', function($value) {
                $parSeTitle = explode('/', $value);
                return [trim($parSeTitle[0]??''), trim($parSeTitle[1]??'')];
            }],
            'Edition' => 'edition',
            'PublishYear' => 'publish_year',
            'PhysicalDescription' => 'spec_detail_info',
            'ISBN' => 'isbn_issn',
            'CallNumber' => 'call_number',
            'Note' => 'notes',
            'DeweyNo' => 'classification',
            'CreateBy' => 'uid',
            'CreateDate' => 'input_date',
            'UpdateDate' => 'last_update'
        ];

        foreach (Catalog::cursor() as $catalog) {
            $catalog->transferTo('Senayan\Biblio', $map)->toOther();
        }


    }
}