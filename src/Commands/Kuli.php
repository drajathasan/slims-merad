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
    protected string $signature = 'merad:dari {migratorname}';

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

            $this->setBanner();

            $migrator->setConsole($this)->migrate();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
        }
    }

    private function setBanner()
    {
        $this->output->write(" __  __                    _ _ 
|  \/  | ___ _ __ __ _  __| | |
| |\/| |/ _ \ '__/ _` |/ _` | |
| |  | |  __/ | | (_| | (_| |_|
|_|  |_|\___|_|  \__,_|\__,_(_)

version v1.0.0

");
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            "Hai, plugin dibuat dengan lisensi GPL versi 3 dengan kata lain plugin ini hadir tanpa ada garansi terkait kesuksesan hasilnya \nmaka jangan lupa melakukan backup data anda sebelum menjalankan migrator ini.\n\nApakah anda setuju 🤔?",
            // choices can also be PHP objects that implement __toString() method
            ['Y' => 'Ya', 'T' => 'Tidak'],
            0
        );

        $question->setErrorMessage('Pilih antara Ya atau Tidak 😧');

        $response = $helper->ask($this->input, $this->output, $question);

        if (strtoupper(substr($response, 0,1)) == 'T') {
            $this->output->write("Yah anda tidak setuju 😔 \n");
        }
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getInput()
    {
        return $this->input;
    }
} 