<?php

namespace LoRDFM\Raw\Commands;

use Illuminate\Console\Command;
use LoRDFM\Raw\Raw;

class RepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raw:repository {models?*} {--force} {--test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repository pattern boilerplate based on Annotated Models';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        $models = $this->argument('models');
        $options = $this->options();

        $raw = new Raw($models, $options);
        $raw->run();
    }
}
