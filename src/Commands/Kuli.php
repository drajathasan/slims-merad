<?php
namespace SLiMS\Merad\Commands;

use SLiMS\Cli\Command;

class Kuli extends Command
{
    /**
     * Signature is combination of command name
     * argument and options
     *
     * @var string
     */
    protected string $signature = 'kuli:angkat {migratorname}';

    /**
     * Command description
     *
     * @var string
     */
    protected string $description = 'Pindahin data otomasi anda ke SLiMS';

    /**
     * Handle command process
     *
     * @return void
     */
    public function handle()
    {
        $class = '\SLiMS\Merad\Migrators\\' . ($migratorName = ucfirst($this->argument('migratorname')??'Uknown'));

        try {
            $migrator = new $class;
            $migrator->migrate();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
        }

    }
} 