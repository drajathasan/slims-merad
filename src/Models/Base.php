<?php
namespace SLiMS\Merad\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Base extends Model
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
                $this->$dest = is_array($callBack = $formattedValue($value)) ? ($formattedValue($value)[0]??'') : $callBack;
                continue;
            }

            $this->$dest = $this->sourceModelInstance->$original;
        }

        $this->save();
        return $this;
    }

    public static function createOrGetIfExists(string $data, array $detail)
    {
        if (empty($data)) return;

        list(
            $sourceModel, 
            $primaryKey,
            $criteria,
            $lastBiblioId,
            $fields
        ) = array_values($detail);

        $ids = [];
        $datas = explode(';', $data);
        foreach ($datas as $eachData) {
            $model = self::where($criteria, $eachData)->first();

            if ($model === null) {
                $model = new static;

                $newFields = [];
                foreach ($fields as $seq => $key) {
                    if (is_array($key)) {
                        $newFields[$key[0]] = $key[1];
                        continue;
                    }
                    $newFields[$key] = substr($eachData, 0,30);
                }
                $model->insertOrIgnore($newFields);
            }

            $model->toManyById([
                $primaryKey => $model->$primaryKey,
                'biblio_id' => $lastBiblioId
            ]);

            $ids[] = $model->$primaryKey;
        }

        return $ids;
    }

    abstract protected function toManyById(array $ids = []);
}