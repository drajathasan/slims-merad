<?php
namespace SLiMS\Merad\Commands;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

use SLiMS\Cli\Command;

class Kuli extends Command
{
    /**
     * Signature is combination of command name
     * argument and options
     *
     * @var string
     */
    protected string $signature = 'merad:dari {migratorname} {--y|ya}';

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
            // Load from custom migrators
            if (!class_exists($class)) {
                $class = '\Customs\Migrators\\' . $migratorName;

                // or load from anthoer class
                if (!class_exists($class)) {
                    $class = $this->argument('migratorname');
                }
            }

            $migrator = new $class;

            if ($this->isAggree() === false)
                if ($this->setBanner() === 0) return 0;

            $migrator->setConsole($this)->migrate();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
        }
    }

    private function setBanner()
    {
        $merad_version = MERAD_VERSION;
        $this->output->write(" __  __                    _ _ 
|  \/  | ___ _ __ __ _  __| | |
| |\/| |/ _ \ '__/ _` |/ _` | |
| |  | |  __/ | | (_| | (_| |_|
|_|  |_|\___|_|  \__,_|\__,_(_)

version {$merad_version}

");
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            "Hai, plugin dibuat dengan lisensi GPL versi 3 dengan kata lain plugin ini hadir tanpa ada garansi terkait kesuksesan hasil proses migrasi yang anda lakukan \nmaka jangan lupa melakukan backup data anda sebelum menjalankan migrator ini.\n\nApakah anda setuju 🤔?",
            // choices can also be PHP objects that implement __toString() method
            ['y' => 'Ya', 't' => 'Tidak'],
            0
        );

        $question->setErrorMessage('Pilih antara Ya atau Tidak 😧');

        $response = $helper->ask($this->input, $this->output, $question);

        if (strtoupper(substr($response, 0,1)) == 'T') {
            $this->output->write("Yah anda tidak setuju 😔 \n");
            return 0;
        }

        return 1;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function isAggree()
    {
        $inputAsString = (string)$this->input;
        $eachInput = array_pop(explode(' ', $inputAsString));
        
        return (bool)preg_match('/(-y|--ya)/', $eachInput);
    }
} 