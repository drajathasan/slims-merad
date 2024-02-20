<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Contract;

class Biblio extends Contract
{
    protected $table = 'biblio';
    protected $primaryKey = 'biblio_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';

    public function toManyById(array $ids = [])
    {
        $lastBiblioId = $this->biblio_id;
        $source = $this->sourceModelInstance;

        if (isset($source->Author)) Author::createOrGetIfExists($source->Author, $this, $lastBiblioId);
        if (isset($source->Subject)) Subject::createOrGetIfExists($source->Subject, $this, $lastBiblioId);
    }
}