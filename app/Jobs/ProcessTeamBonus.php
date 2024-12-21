<?php

namespace App\Jobs;

use App\Models\Member;
use App\Http\Controllers\MemberController; // Import the controller
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTeamBonus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $memberId; // Store member ID
    protected $rand;
    public $tries = 5;

    // Constructor to pass member id and rand values
    public function __construct($memberId, $rand)
    {
        $this->memberId = $memberId;  // Store only the ID
        $this->rand = $rand;
    }

    public function handle()
    {
        // Retrieve the member using the ID
        $member = Member::find($this->memberId);

        // Log what we received
        Log::info('Member fetched:', ['member' => $member]);

        // Ensure that a member object is found
        if ($member instanceof Member) {
            // Instantiate the MemberController
            $memberController = new MemberController();

            // Call the teamBonus function in MemberController
            $memberController->teamBonus($member, $this->rand);
        } else {
            // Log an error if the member is not found
            Log::error('Member not found or invalid type. ID: ' . $this->memberId);
        }
    }
    
    public function backoff()
    {
        return [300]; // 5 minutes
    }
}
