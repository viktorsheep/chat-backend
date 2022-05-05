<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AutoDeploy extends Command
{
  /**
   * The name and signature of the console command.
    *
    * @var string
    */
  protected $signature = 'git:deploy {type}';

  /**
   * The console command description.
    *
    * @var string
    */
  protected $description = 'Git deploy base...';

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
    try {
      // Prepping Commands
      $commands = [
        "dir" => $this->argument('type'),
        "branch" => $this->argument('branch-name'),
        "extra" => []
      ];

      // Adding Extras
      if($this->argument('type') === 'gate') {
        $commands["extra"] = [
          "php artisan migrate"
        ];
      }

      // Prepping Processes
      $processes = [
        'cd /var/www/' . $commands["dir"],
        'git fetch --all',
        'git pull origin ' . $commands["branch"]
      ];

      // Prepping Extras
      $extra = $commands["dir"] === 'web'
        ? ''
        : ' && ' . join(' && ', $commands["extra"]);

      $process = join(' && ', $processes) . $extra;

      // Executing Commands
      exec($process, $o, $r);
      $this->info(json_encode($o));

    } catch (Exception $e) {
      $this->error($e);
    }
  }
}