<?php
namespace SLiMS\Merad\Migrators;

use Symfony\Component\Console\Helper\ProgressBar;

abstract class Contract
{
    protected ?object $console = null;
    protected ?object $output = null;
    protected ?object $input = null;
    protected ?object $progress = null;

    public function setConsole(Object $console)
    {
        $this->console = $console;
        $this->output = $this->console->getOutput();
        $this->input = $this->console->getInput();
        return $this;
    }

    public function setProgress(int $unit)
    {
        $this->progress = null;
        $this->progress = new ProgressBar($this->output, $unit);
    }

    abstract public function migrate();
}