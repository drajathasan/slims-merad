<?php
namespace SLiMS\Merad\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Contract extends Model
{
    public function transferTo(string $anotherModelNameOrClass, array $columnMap)
    {
        if (class_exists($finalClass = '\SLiMS\Merad\Models\\' . $anotherModelNameOrClass)) {
            $instance = new $finalClass;
        } else if (class_exists($anotherModelNameOrClass)) {
            $instance = new $anotherModelNameOrClass;
        }

        return $instance->retriveAndStore($this, $columnMap);
    }

    public function retriveAndStore(object $sourceModelInstance, array $columnMap)
    {
        dd(func_get_args());
    }
}