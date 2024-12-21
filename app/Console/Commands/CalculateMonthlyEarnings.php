<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MemberController;

class CalculateMonthlyEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:calculate-earnings';
    protected $description = 'Calculate Monthly Team Performance Bonus for all members';

    
    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new MemberController();
        $controller->calculateMonthlyTeamPerformanceBonus();

        $this->info('Monthly earnings calculation completed.');
    }

}
