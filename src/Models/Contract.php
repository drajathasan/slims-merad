<?php
namespace SLiMS\Merad\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Contract extends Model
{
    protected ?object $sourceModelInstance = null;

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
        $this->sourceModelInstance = $sourceModelInstance;

        foreach ($columnMap as $original => $dest) {
            $value = $this->sourceModelInstance->$original;

            if (is_array($dest)) {
                list($dest, $formattedValue) = $dest;
                $this->$dest = $formattedValue($value)[0]??'';
                continue;
            }

            $this->$dest = $this->sourceModelInstance->$original;
        }

        $this->save();
        return $this;
    }

    abstract protected function toManyById(array $ids = []);
}