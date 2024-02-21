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

            Schema::table('mst_topic', function(Blueprint $table) {
                $table->string('topic', 700)->notNull()->change();
            });

            Schema::table('mst_place', function(Blueprint $table) {
                $table->string('place_name', 700)->notNull()->change();
            });

            Schema::table('mst_author', function(Blueprint $table) {
                $table->string('author_name', 700)->notNull()->change();
            });
        }

        $map = [
            'ID' => 'inID',
            'Title' => ['title', function($value) {
                $parSeTitle = explode('/', $value);
                return [trim($parSeTitle[0]??''), trim($parSeTitle[1]??'')];
            }],
            'Edition' => ['edition', function($value) {
                $value = substr($value, 0,50);
                return [trim($value), trim($value)];
            }],
            'PublishYear' => ['publish_year', function($value) {
                $value = substr($value, 0,4);
                return [trim($value), trim($value)];
            }],
            'PhysicalDescription' => 'spec_detail_info',
            'ISBN' => 'isbn_issn',
            'CallNumber' => ['call_number', function($value) {
                $value = substr($value, 0,50);
                return [trim($value), trim($value)];
            }],
            'Note' => 'notes',
            'DeweyNo' => ['classification', function($value) {
                $value = substr($value, 0,50);
                return [trim($value), trim($value)];
            }],
            'CoverURL' => 'image',
            'CreateBy' => 'uid',
            'CreateDate' => 'input_date',
            'UpdateDate' => 'last_update'
        ];

        $total = Catalog::count();
        foreach (Catalog::cursor() as $seq => $catalog) {
            $seq = $seq + 1;
            $catalog->transferTo('Senayan\Biblio', $map)->toManyById();
            $precentage = $seq / $total * 100;
            echo 'Selesai memproses ' . $catalog->Title . ' ' . $seq . '/' . $total . ' - ' . $precentage . '%' . PHP_EOL;
        }


    }
}