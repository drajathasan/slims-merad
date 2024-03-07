<?php
namespace SLiMS\Merad\Migrators;

use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;

/* SLiMS Models */
use SLiMS\Merad\Models\Senayan\Biblio;
use SLiMS\Merad\Models\Senayan\SearchBiblio;
use SLiMS\Merad\Models\Senayan\Member;
use SLiMS\Merad\Models\Senayan\Loan;
/* End SLiMS Models */

use SLiMS\Merad\Models\Inlis\Catalog;
use SLiMS\Merad\Models\Inlis\Collection;
use SLiMS\Merad\Models\Inlis\Memberis;
use SLiMS\Merad\Models\Inlis\Collectionloanitem;

/* Etc */
use Symfony\Component\Console\Helper\ProgressBar;

class Inlis extends Contract
{
    private ?object $console = null;
    private ?object $output = null;
    private ?object $input = null;
    private ?object $progress = null;

    public function setConsole(Object $console)
    {
        $this->console = $console;
        $this->output = $this->console->getOutput();
        $this->input = $this->console->getInput();
        return $this;
    }

    public function setProgress(int $unit)
    {
        $this->progress = null;
        $this->progress = new ProgressBar($this->output, $unit);
    }

    public function migrate()
    {
        $this->console->info('Memigrasikan Catalog ke Biblio');
        $this->catalogToBiblio();

        $this->console->info('Memigrasikan Collection ke Item');
        $this->collectionToItem();

        $this->console->info('Melakukan proses indexing data bibliografi ke mesin pencari');
        $this->makeIndex();

        $this->console->info('Memigrasikan Members ke Member');
        $this->memberToMember();

        $this->console->info('Memigrasikan Collection Loan Items ke Loan');
        $this->collectionloanitemsToLoan();

        $this->console->success('Yay berhasil 🎉😁');
        $this->progress?->finish();
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
        $this->setProgress($total);

        foreach (Catalog::cursor() as $seq => $catalog) {
            $this->progress->advance();
            $catalog->transferTo('Senayan\Biblio', $map)->toManyById();
        }

        // $this->progress->finish();
        echo PHP_EOL . PHP_EOL;
    }

    private function collectionToItem()
    {
        if (!Schema::hasColumn('item', 'inID')) {
            Schema::table('item', function(Blueprint $table) {
                $table->number('inID', 11)->nullable()->add();
            });
        }

        $map = [
            'NomorBarcode' => 'item_code',
            'Currency' => 'price_currency',
            'Price' => 'price',
            'TanggalPengadaan' => 'received_date',
            'CallNumber' => 'call_number',
            'CreateDate' => 'input_date',
            'UpdateDate' => 'last_update',
            'CreateBy' => 'uid',
            'ID' => 'inID'
        ];

        $total = Collection::count();
        $this->setProgress($total);

        foreach (Collection::cursor() as $seq => $collection) {
            $this->progress->advance();
            $collection->transferTo('Senayan\Item', $map)->updateId();
        }

        echo PHP_EOL . PHP_EOL;
    }

    private function makeIndex()
    {
        SearchBiblio::truncate();
        $total = Biblio::count();
        $this->setProgress($total);

        foreach (Biblio::cursor() as $biblio) {
            $biblio->makeIndex($biblio->biblio_id);
            $this->progress->advance();
        }

        echo PHP_EOL . PHP_EOL;
    }

    private function memberToMember()
    {
        if (!Schema::hasColumn('member', 'inID')) {
            Schema::table('member', function(Blueprint $table) {
                $table->number('inID', 11)->default(0)->add();
                $table->index('inID')->add();
            });
        }

        $map = [
            'ID' => 'inID',
            'MemberNo' => 'member_id',
            'Fullname' => 'member_name',
            'DateOfBirth' => 'birth_date',
            'Address' => 'member_address',
            'NoHp' => 'member_phone',
            'IdentityType_id' => 'member_type_id',
            'Sex_id' => 'gender',
            'RegisterDate' => 'member_since_date',
            'register_date' => 'member_since_date',
            'EndDate' => 'expire_date',
            'Email' => 'member_email',
            'CreateDate' => 'input_date'
        ];

        $total = Memberis::count();
        $this->setProgress($total);

        foreach (Memberis::cursor() as $member) {
            $this->progress->advance();
            $member->transferTo('Senayan\Member', $map)->toManyById();
        }

        echo PHP_EOL . PHP_EOL;
    }

    private function collectionloanitemsToLoan()
    {
        if (!Schema::hasColumn('loan', 'inID')) {
            Schema::table('loan', function(Blueprint $table) {
                $table->number('inID', 11)->default(0)->add();
                $table->index('inID')->add();
            });
        }

        $map = [
            'Collection_id' => ['item_code', function($value) {
                return Collection::find($value)?->NomorBarcode??null;
            }],
            'member_id' => ['member_id', function($value) {
                return Memberis::find($value)?->MemberNo??null;
            }],
            'LoanDate' => 'loan_date',
            'DueDate' => 'due_date',
            'ActualReturn' => 'return_date',
            'CreateBy' => 'uid',
            'CreateDate' => 'input_date',
            'UpdateDate' => 'last_update',
            'ID' => 'inID'
        ];

        $total = Collectionloanitem::count();
        $this->setProgress($total);

        foreach (Collectionloanitem::cursor() as $loanItem) {
            $this->progress->advance();
            $loanItem->transferTo('Senayan\Loan', $map)->toManyById();
        }

        echo PHP_EOL . PHP_EOL;
    }
}