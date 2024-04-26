<?php
namespace SLiMS\Merad\Commands;

use SLiMS\Cli\Command;
use SLiMS\Filesystems\Storage;

class Custom extends Command
{
    /**
     * Signature is combination of command name
     * argument and options
     *
     * @var string
     */
    protected string $signature = 'merad:buat:migrator {migratorname}';

    /**
     * Command description
     *
     * @var string
     */
    protected string $description = 'Membuat direktori customs migrator';

    /**
     * Handle command process
     *
     * @return void
     */
    public function handle()
    {
        $pluginStorage = Storage::plugin();
        
        if (!$pluginStorage->isExists($basePath = MERAD_BASENAME . 'customs' . DS)) {
            $pluginStorage->makeDirectory($basePath . 'Migrators');
            $pluginStorage->makeDirectory($basePath . 'Models/Senayan');
        }

        $this->createMigrator($pluginStorage, $basePath);
    }

    private function createMigrator($pluginStorage, $customPath)
    {
        $template = $pluginStorage->read(MERAD_BASENAME . 'src/template/migrator');
        $migratorPath = $customPath . 'Migrators/' . ($migratorName = ucfirst($this->argument('migratorname'))) . '.php';

        if (!$pluginStorage->isExists($migratorPath)) {
            $pluginStorage->put($migratorPath, str_replace('<migratorname>', $migratorName, $template));
            $this->success('Berhasil membuat custom migrator 😉');
        } else {
            $this->success('Migrator ' . $migratorName . ' sudah ada 😔');
        }
    }
}