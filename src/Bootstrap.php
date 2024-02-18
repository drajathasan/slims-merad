<?php
namespace SLiMS\Merad;

use Illuminate\Database\Capsule\Manager as Capsule;

final class Bootstrap
{
    private ?Capsule $capsule = null;

    public static function setupDatabase()
    {
        $static = new static;
        $databaseConfig = config('database');
        $defailtDatabase = $databaseConfig['default_profile'];

        $static->capsule = new Capsule;

        foreach ($databaseConfig['nodes'] as $nodeName => $detail) {
            if ($nodeName === $defailtDatabase) $nodeName = 'default';
            if (!isset($detail['driver'])) $detail['driver'] = 'mysql';
            $detail['charset'] = 'utf8';
            $detail['collation'] = 'utf8_unicode_ci';

            $static->capsule->addConnection($detail, $nodeName);
        }
        $static->capsule->setAsGlobal();

        return $static;
    }

    public function withOrm()
    {
        $this->capsule->bootEloquent();
    }
}