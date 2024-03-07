<?php
namespace SLiMS\Merad\Models\Senayan;

use SLiMS\Merad\Models\Base;
use SLiMS\Merad\Models\Inlis\Worksheet;

class Loan extends Base
{
    protected $table = 'loan';
    protected $primaryKey = 'loan_id';
    const UPDATED_AT = 'last_update';
    const CREATED_AT = 'input_date';
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function toManyById(array $ids = [])
    {
        $source = $this->sourceModelInstance;

        if ($source->LoanStatus === 'Loan') {
            $this->is_lent = 1;
        } else {
            $this->is_lent = 0;
            $this->is_return = 1;
        }

        // $this->loan_rules_id = 1;

        $this->save();
    }
}