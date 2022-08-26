<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Balance;
use App\Models\Transactions;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Settings;

class Queue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for jobs every 1 minutes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings = Settings::find(1);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();

        $this->call('queue:work', ['--stop-when-empty' => 1]);
        $this->info('Done!!!');
    }
}
