<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeamBonusRequest;
use App\Models\Member;
use App\Http\Controllers\MemberController;  // Import your MembersController

class ProcessTeamBonuses extends Command
{
    // The name and signature of the console command.
    protected $signature = 'process:team-bonuses';

    // The console command description.
    protected $description = 'Process all pending team bonus requests';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Fetch all pending team bonus requests
        $requests = TeamBonusRequest::whereNull('processed_at')->get();

        // Check if we have any requests to process
        if ($requests->isEmpty()) {
            $this->info('No pending team bonus requests found.');
            return;
        }

        // Instantiate the MembersController
        $memberController = new MemberController();

        foreach ($requests as $request) {
            // Call the teamBonus method on the MemberController
            // Pass the member and the rand value
            $member = Member::find($request->member_id);
            $rand = $request->rand;

            // Call the method from the controller
            try {
                $memberController->teambonus($member, $rand);

                // Mark the request as processed
                $request->update(['processed_at' => now()]);

                $this->info("Processed bonus for member ID: {$member->id}.");
            } catch (\Exception $e) {
                // Handle any errors gracefully
                $this->error("Failed to process bonus for member ID: {$member->id}. Error: {$e->getMessage()}");
            }
        }

        $this->info('All pending team bonuses have been processed.');
    }
}
