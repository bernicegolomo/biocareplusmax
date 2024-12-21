<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessTeamBonus;


class Member extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'username', 'phone', 'address', 'country', 'password', 'referrer_id', 'left_leg_id', 'right_leg_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    

    public function earnings()
    {
        return $this->hasMany(Earning::class);
    }

    public function debits()
    {
        return $this->hasMany(Debit::class);
    }

    public function getTotalEarnings($type, $year=null)
    {
        if($year !=null){
            return $this->earnings()->where('type', $type)->whereYear('created_at', $year)->sum('value');
        }
        return $this->earnings()->where('type', $type)->sum('value');
    }

    public function getTotalDebit($type, $year=null)
    {
        if($year !=null){
            return $this->debits()->where('type', $type)->whereYear('created_at', $year)->sum('value');
        }
        
        //$data =  $this->debits()->where('type', $type)->get();
        //dd($data);
        return $this->debits()->where('type', $type)->sum('value');
    }

     // Calculate balance
     public function getBalance($type = null, $fromdate = null, $todate = null)
    {
        // Initialize earnings and debits queries
        $earningsQuery = $this->earnings();
        $debitsQuery = $this->debits();

        // Apply filters based on the type, fromdate, and todate
        if ($type !== null) {
            $earningsQuery->where('type', $type);
            $debitsQuery->where('type', $type);
        }

        if ($fromdate !== null && $todate !== null) {
            $earningsQuery->whereBetween('created_at', [$fromdate, $todate]);
            $debitsQuery->whereBetween('created_at', [$fromdate, $todate]);
        }

        // Calculate total earnings and debits
        $totalEarnings = $earningsQuery->sum('value');
        $totalDebits = $debitsQuery->sum('value');

        // Return the balance
        return $totalEarnings - $totalDebits;
    }

    public function referrer()
    {
        return $this->belongsTo(Member::class, 'referrer_id');
    }
    

    public function addEarnings($points,$type,$desc,$settlement_id=null,$rand=null)
    {
        if($rand == null){ $rand = "";}
        
        if($points > 0){
            if($settlement_id != null){
                $this->earnings()->create([
                    'value'                 => $points,
                    'type'                  => $type,
                    'description'           => $desc,
                    'settlement_member_id'  => $settlement_id,
                    'transaction_id'        => $rand
                ]);
            }else{
                $this->earnings()->create([
                    'value'                 => $points,
                    'type'                  => $type,
                    'description'           => $desc,
                    'transaction_id'        => $rand
                ]);
            }
        }
    }

    public function addDebit($points,$type,$desc,$rand=null)
    {
        if($rand == null){ $rand = "";}
        
        if($points > 0){
            $this->debits()->create([
                'value'             => $points,
                'type'              => $type,
                'description'       => $desc,
                'transaction_id'    => $rand
            ]);
        }

    }
    
    public function leftLeg()
    {
        return $this->belongsTo(Member::class, 'left_leg_id');
    }
    
    public function rightLeg()
    {
        return $this->belongsTo(Member::class, 'right_leg_id');
    }
    
    
    public function descendants()
    {
        return $this->hasMany(Member::class, 'referrer_id');
    }



    public function referredMembers()
    {
        return $this->hasMany(Member::class, 'referrer_id');
    }

    public function isPlaced()
    {
        return $this->left_leg_id !== null && $this->right_leg_id !== null;
    }

   // Count all left descendants
    public function countLeftDescendants()
    {
        return $this->countDescendants($this->leftLeg);
    }

    // Count all right descendants
    public function countRightDescendants()
    {
        return $this->countDescendants($this->rightLeg);
    }

    // Recursive function to count descendants
    private function countDescendants($node)
    {
        if (!$node) {
            return 0;
        }

        Log::info('Processing Member ID: ' . $node->id);

        $leftCount = $this->countDescendants($node->leftLeg);
        $rightCount = $this->countDescendants($node->rightLeg);

        // Return the sum of left and right descendants plus one for the current node
        $count = (1 + $leftCount + $rightCount);
        
        return $count;
    }
    
    public function getTreeMembers()
    {
        $members = collect();
        $members->push($this); // Include the current member

        // Collect downlines
        $this->collectDescendantz($members);

        // Collect uplines
        $currentMember = $this;
        while ($currentMember->referrer_id) {
            $referrer = Member::find($currentMember->referrer_id);
            if ($referrer) {
                $members->push($referrer);
                $currentMember = $referrer;
            } else {
                break;
            }
        }

        return $members->unique();
    }

    private function collectDescendantz($members)
    {
        $leftLeg = $this->leftLeg;
        if ($leftLeg) {
            $members->push($leftLeg);
            $leftLeg->collectDescendantz($members);
        }

        $rightLeg = $this->rightLeg;
        if ($rightLeg) {
            $members->push($rightLeg);
            $rightLeg->collectDescendantz($members);
        }
    }
    
    public function getPackageBonusDetails($package)
    {
        $packages = [
            'Associate' => ['bonus_per_pair' => 1000, 'daily_cap' => 10000, 'weekly_cap' => 70000, 'monthly_cap' => 280000],
            'Basic' => ['bonus_per_pair' => 1200, 'daily_cap' => 18000, 'weekly_cap' => 126000, 'monthly_cap' => 504000],
            'Super' => ['bonus_per_pair' => 1500, 'daily_cap' => 30000, 'weekly_cap' => 210000, 'monthly_cap' => 840000],
            'Premium' => ['bonus_per_pair' => 2200, 'daily_cap' => 55000, 'weekly_cap' => 385000, 'monthly_cap' => 1540000],
        ];
    
        return $packages[$package->name] ?? $packages['Associate']; // Default to Associate if no package match
    }
    
    public function isEligibleForBonus($member, $level, $Lkey)
    {
        // Check if member has referred one person on both legs
        $hasReferredBothLegs = $member->hasReferredOnBothLegs();
    
        // Check if the member has met the monthly volume requirement
        $meetsMonthlyRequirement = $member->meetsMonthlyRepurchaseVolume();
    
        // Ensure the member has been registered for at least one month
        $registeredOneMonthAgo = $member->created_at->lt(now()->subMonth());
    
        return $hasReferredBothLegs && $meetsMonthlyRequirement && $registeredOneMonthAgo;
    }
    
    public function getSmallerLeg($level)
    {
        return $level['left'] <= $level['right'] ? 'left' : 'right';
    }
    

    public function getAllUplinesWithCounts($newMemberId)
    {
        $results = [];
        $currentMember = $this; 
        
        

        while ($currentMember) {
            $level = $this->getMemberLevel($currentMember->id);
            $counts = $this->countMembersAtLevels($currentMember->id);
            
            foreach ($counts as $lvl => &$count) {
                if ($count['left'] > 0 && $count['right'] > 0) {
                    if ($this->legBalancesLevel($currentMember->id, $lvl, $newMemberId)) { 
                        $count['balanced_leg'] = $this->getBalancingLeg($currentMember->id, $lvl, $newMemberId);
                        $count['balancing_member'] = $newMemberId;
                    } else {
                        $count['balanced_leg'] = null;
                        $count['balancing_member'] = null;
                    }
                } else {
                    $count['balanced_leg'] = null;
                    $count['balancing_member'] = null;
                }
            }

            // Count for left and right legs directly from the current member
            $leftCount = $this->countMembersOnLeg($currentMember->id, 'left');
            $rightCount = $this->countMembersOnLeg($currentMember->id, 'right');

            // Collect the results
            $results[$currentMember->id] = [
                'level' => $level,
                'left' => $leftCount,
                'right' => $rightCount,
                'counts' => $counts,
            ];

            // Move to the next upline
            $currentMember = $currentMember->referrer_id ? self::find($currentMember->referrer_id) : null;
            
            /*$currentMember = self::where('left_leg_id', $currentMember->id)
                                ->orWhere('right_leg_id', $currentMember->id)
                                ->first();*/
        }

        return $results;
    }
    
    public function getAllUplinesWithCountss($newMemberId)
    {
        $results = [];
        $currentMember = $this;
    
        // Initialize an array to keep track of processed members to avoid loops
        $processedMembers = [];
    
        // Use a loop to find all uplines
        while ($currentMember) {
            // Get the member level and counts for the current member
            $level = $this->getMemberLevel($currentMember->id);
            $counts = $this->countMembersAtLevels($currentMember->id);
            $total = 0;
    
            // Process counts and determine balanced leg
            foreach ($counts as $lvl => &$count) {
                if ($count['left'] > 0 && $count['right'] > 0) {
                    if ($this->legBalancesLevel($currentMember->id, $lvl, $newMemberId)) {
                        $count['balanced_leg'] = $this->getBalancingLeg($currentMember->id, $lvl, $newMemberId);
                        $count['balancing_member'] = $newMemberId;
                    } else {
                        $count['balanced_leg'] = null;
                        $count['balancing_member'] = null;
                    }
                    $count['total'] = $total++;
                } else {
                    $count['balanced_leg'] = null;
                    $count['balancing_member'] = null;
                    $count['total'] = "";
                }
            }
    
            // Count for left and right legs directly from the current member
            $leftCount = $this->countMembersOnLeg($currentMember->id, 'left');
            $rightCount = $this->countMembersOnLeg($currentMember->id, 'right');
    
            // Collect the results for the current member
            $results[$currentMember->id] = [
                'level' => $level,
                'left' => $leftCount,
                'right' => $rightCount,
                'counts' => $counts,
            ];
    
            // Add the current member to the processed list to avoid loops
            $processedMembers[] = $currentMember->id;
    
            // Fetch all uplines for the current member at once
            $uplines = Member::where('left_leg_id', $currentMember->id)
                             ->orWhere('right_leg_id', $currentMember->id)
                             ->get();
    
            // Check if there are any uplines
            if ($uplines->isEmpty()) {
                break; // No more uplines, exit the loop
            }
    
            // Move to the first upline (assuming you want to follow the first one)
            $currentMember = $uplines->first();
    
            // Optional: If you want to process all uplines instead of just the first, 
            // you can iterate over the $uplines collection
        }
    
        return $results;
    }

    
    public function getAllUplinesWithCountssLastworking($newMemberId)
    {
        $results = [];
        $currentMember = $this; 
        
        

        while ($currentMember) {
            $level = $this->getMemberLevel($currentMember->id);
            $counts = $this->countMembersAtLevels($currentMember->id);
            $total = 0;
            foreach ($counts as $lvl => &$count) { 
                
                if ($count['left'] > 0 && $count['right'] > 0) {
                    if ($this->legBalancesLevel($currentMember->id, $lvl, $newMemberId)) { 
                        $count['balanced_leg'] = $this->getBalancingLeg($currentMember->id, $lvl, $newMemberId);
                        $count['balancing_member'] = $newMemberId;
                        
                    } else {
                        $count['balanced_leg'] = null;
                        $count['balancing_member'] = null;
                    }
                    $count['total'] = $total++; 
                } else {
                    $count['balanced_leg'] = null;
                    $count['balancing_member'] = null;
                    $count['total'] = "";
                }
            }

            // Count for left and right legs directly from the current member
            $leftCount = $this->countMembersOnLeg($currentMember->id, 'left');
            $rightCount = $this->countMembersOnLeg($currentMember->id, 'right');

            // Collect the results
            $results[$currentMember->id] = [
                'level' => $level,
                'left' => $leftCount,
                'right' => $rightCount,
                'counts' => $counts,
            ];
            
            $upline = Member::where('left_leg_id', $currentMember->id)
                            ->orWhere('right_leg_id', $currentMember->id)
                            ->first();
    
            if ($upline) {
                $uplines[] = $upline; // Add upline to the list
                $currentMember = $upline; // Move up to the next upline
            } else {
                break; // No more uplines, exit the loop
            }

            // Move to the next upline
            // $currentMember = $currentMember->referrer_id ? self::find($currentMember->referrer_id) : null;
            
            /*$currentMember = self::where('left_leg_id', $currentMember->id)
                                ->orWhere('right_leg_id', $currentMember->id)
                                ->first();*/
        }

        return $results;
    }



    private function getMemberLevel($memberId)
    {
        $level = 1;
        $member = self::find($memberId);

        while ($member && $member->referrer_id) {
            $member = self::find($member->referrer_id);
            $level++;
        }

        return $level;
    }
    
    /*private function getMemberLevel($memberId)
    {
        $level = 1;
        $member = self::find($memberId);
    
        while ($member) {
            // Find the member who has this member as their left_leg_id or right_leg_id
            $upline = self::where('left_leg_id', $member->id)
                        ->orWhere('right_leg_id', $member->id)
                        ->first();
    
            if (!$upline) {
                break; // Exit the loop if no upline is found
            }
    
            $member = $upline;
            $level++;
        }
    
        return $level;
    }*/


    private function countMembersAtLevels($memberId)
    {
        $counts = [];
        $level = 1;
        $membersAtLevel = [ $memberId ];
        
        while (!empty($membersAtLevel)) {
            $leftCount = 0;
            $rightCount = 0;
            $nextLevelMembers = [];
            
            foreach ($membersAtLevel as $memberId) {
                $member = self::find($memberId);
                
                if ($member) {
                    if ($member->left_leg_id) {
                        $leftCount++;
                        $nextLevelMembers[] = $member->left_leg_id;
                    }
                    if ($member->right_leg_id) {
                        $rightCount++;
                        $nextLevelMembers[] = $member->right_leg_id;
                    }
                }
            }
            
            $counts[$level] = [
                'left' => $leftCount,
                'right' => $rightCount,
                'balanced_leg' => null,
                'balancing_member' => null,
            ];

            $membersAtLevel = $nextLevelMembers;
            $level++;
        }

        return $counts;
    }

    private function countMembersOnLeg($memberId, $leg)
    {
        $count = 0;
        $queue = [$memberId];
        
        while (!empty($queue)) {
            $currentId = array_shift($queue);
            $currentMember = self::find($currentId);

            if ($leg === 'left' && $currentMember->left_leg_id) {
                $count++;
                $queue[] = $currentMember->left_leg_id;
            } elseif ($leg === 'right' && $currentMember->right_leg_id) {
                $count++;
                $queue[] = $currentMember->right_leg_id;
            }
        }

        return $count;
    }

    private function legBalancesLevel($memberId, $level, $newMemberId)
    {
        $membersAtLevel = [ $memberId ];
        for ($lvl = 1; $lvl < $level; $lvl++) {
            $nextLevelMembers = [];
            foreach ($membersAtLevel as $memberId) {
                $member = self::find($memberId);
                if ($member) {
                    if ($member->left_leg_id) {
                        $nextLevelMembers[] = $member->left_leg_id;
                    }
                    if ($member->right_leg_id) {
                        $nextLevelMembers[] = $member->right_leg_id;
                    }
                }
            }
            $membersAtLevel = $nextLevelMembers;
        }

        foreach ($membersAtLevel as $memberId) {
            $member = self::find($memberId);
            if ($member->left_leg_id == $newMemberId || $member->right_leg_id == $newMemberId) {
                return true;
            }
        }

        return false;
    }

    private function getBalancingLeg($memberId, $level, $newMemberId)
    {
        $membersAtLevel = [$memberId]; 
    
        for ($lvl = 1; $lvl < $level; $lvl++) {
            $nextLevelMembers = [];
            foreach ($membersAtLevel as $currentMemberId) {
                $member = self::find($currentMemberId);
                if ($member) {
                    if ($member->left_leg_id) {
                        $nextLevelMembers[] = $member->left_leg_id;
                    }
                    if ($member->right_leg_id) {
                        $nextLevelMembers[] = $member->right_leg_id;
                    }
                }
            }
            $membersAtLevel = $nextLevelMembers;
        }
    
        // Backtrack from $newMemberId to find its position relative to the top $memberId
        $currentMemberId = $newMemberId;
        while ($currentMemberId != $memberId) {
            $parentMember = self::where('left_leg_id', $currentMemberId)
                                ->orWhere('right_leg_id', $currentMemberId)
                                ->first();
            if (!$parentMember) {
                return null;  // This shouldn't happen, but just in case
            }
    
            // Determine if $currentMemberId is in the left or right leg
            if ($parentMember->left_leg_id == $currentMemberId) {
                $currentLeg = 'left';
            } elseif ($parentMember->right_leg_id == $currentMemberId) {
                $currentLeg = 'right';
            } else {
                return null;  // Something went wrong, so return null
            }
    
            $currentMemberId = $parentMember->id;
        }
    
        // Return the leg where $newMemberId ultimately resides relative to $memberId
        return $currentLeg;
    }


    private function getAllDownlineIds($memberId)
    {
        $ids = [];

        if ($memberId) {
            $ids[] = $memberId;
            $member = self::find($memberId);

            if ($member) {
                $ids = array_merge($ids, $this->getAllDownlineIds($member->left_leg_id));
                $ids = array_merge($ids, $this->getAllDownlineIds($member->right_leg_id));
            }
        }

        return $ids;
    }
    
    public function getAllDownlineUsernames($memberId)
    {
        $ids = [];
        $usernames = [];
    
        if ($memberId) {
            $member = self::find($memberId);
    
            if ($member) {
                $ids[] = $memberId;
                $usernames[] = $member->username;
    
                $leftDownline = $this->getAllDownlineUsernames($member->left_leg_id);
                $rightDownline = $this->getAllDownlineUsernames($member->right_leg_id);
    
                $ids = array_merge($ids, $leftDownline['ids']);
                $usernames = array_merge($usernames, $leftDownline['usernames']);
    
                $ids = array_merge($ids, $rightDownline['ids']);
                $usernames = array_merge($usernames, $rightDownline['usernames']);
            }
        }
    
        return ['ids' => $ids, 'usernames' => $usernames];
    }
    
        

    public function getAllDownlineDetails($memberId)
{
    $ids = [];

    $member = self::find($memberId);

    if ($member) {
        $ids[] = $member->id;

        // Recursively get downline IDs for left and right legs
        $leftDownlineIds = $this->getAllDownlineDetails($member->left_leg_id);
        $rightDownlineIds = $this->getAllDownlineDetails($member->right_leg_id);

        // Merge results
        $ids = array_merge($ids, $leftDownlineIds, $rightDownlineIds);
    }

    return $ids;
}



    public function getTotalEarningsFromRightLeg($type = null)
    {
        $total = 0;
    
        // Get all downline IDs and usernames
        $downlines = $this->getAllDownlineUsernames($this->right_leg_id);
        $rightLegIds = $downlines['ids'];
        $usernames = $downlines['usernames'];
    
        if (!empty($rightLegIds)) {
            foreach ($rightLegIds as $key => $username) { 
                $query = \DB::table('earnings'); // Reset query for each username
    
                // Apply type filter if provided
                if ($type) {
                    $query->where('type', $type);
                }
    
                // Main condition: match settlement_member_id or description
                $subtotal = $query->where('member_id', $this->id)
                                  ->where('settlement_member_id', $username)
                                  ->where('type', $type)
                                  ->sum('value');
                
                $total += $subtotal;
            }
    
            //dd($total);
        }
    
        return $total;
    }


    public function getTotalEarningsFromLeftLeg($type = null, $from = null, $to = null)
    {
        $total = 0;
    
        // Get all downline IDs and usernames
        $downlines = $this->getAllDownlineUsernames($this->left_leg_id);
        $leftLegIds = $downlines['ids'];
        $usernames = $downlines['usernames'];
    
        if (!empty($leftLegIds)) {
            foreach ($leftLegIds as $username) {
                // Reset query for each iteration
                $query = \DB::table('earnings');
                
                // Apply type filter if provided
                if ($type) {
                    $query->where('type', $type);
                }
                
                if ($from && $to) {
                    $query->whereDate('created_at', '>=', $from)
                          ->whereDate('created_at', '<=', $to);
                }
    
                // Main condition: match settlement_member_id
                $subtotal = $query->where('member_id', $this->id)
                                  ->where('settlement_member_id', $username)
                                  ->sum('value');
                
                // Add to total
                $total += $subtotal;
            }
    
            // dd($total); // Debugging line if needed
        }
    
        return $total;
    }



    public function getLesserLegEarnings($type = null)
    {
        $leftEarnings = $this->getTotalEarningsFromLeftLeg($type);
        $rightEarnings = $this->getTotalEarningsFromRightLeg($type);
        
        
        
        
        if($leftEarnings < $rightEarnings){

            //return "left";
            $lesspoint = "left";
        }else{
            $lesspoint = "right";
        }
        
        return [
                'leftpoint'     => $leftEarnings,
                'rightpoint'    => $rightEarnings,
                'lesserleg'     => $lesspoint,
            ];

        //return "right";
        //return min($leftEarnings, $rightEarnings);
    }


    public function packages()
    {
        return $this->belongsToMany(Package::class, 'members_packages', 'member_id', 'package_id')
                    ->withTimestamps();
    }
    
    public function package()
    {
        return $this->belongsTo(Package::class); 
    }
    
    
    protected function teamBonus($member, $rand)
    {
        DB::beginTransaction(); // Start the transaction
        
        try {
            // Eager load all uplines and their packages in a single query
            $memberTrees = $member->getAllUplinesWithCountss($member->id);
            $uplineIds = array_keys($memberTrees);
        
    
            // Get all uplines at once
            $uplines = Member::whereIn('id', $uplineIds)->with('package')->get()->keyBy('id');
            
            // Get all member packages at once
            $memberPackages = MembersPackage::select('members_packages.*', 'packages.*')
                                        ->join('packages', 'members_packages.package_id', '=', 'packages.id')
                                        ->whereIn('members_packages.member_id', $uplineIds)
                                        ->whereRaw('members_packages.id IN (SELECT MAX(id) FROM members_packages GROUP BY member_id)')
                                        ->orderBy('members_packages.id', 'DESC')
                                        ->get()
                                        ->keyBy('member_id');
    
            $mpackage = $this->getMemberPackage($member->id);
    
            if ($mpackage && count($memberTrees) > 0) {
                foreach ($memberTrees as $memberId => $memberTree) { 
                    if ($memberId != $member->id) {
                        $upline = $uplines[$memberId] ?? null;
    
                        // Use the pre-fetched package
                        $package = $memberPackages[$memberId] ?? null;
    
                        if ($package) {
                            $levels = $memberTree['counts'];
    
                            foreach ($levels as $Lkey => $level) {
                                if ($Lkey != 1 && isset($levels[1]) && $levels[1]["left"] >= 1 && $levels[1]["right"] >= 1) {
                                    // Cache the qualification check to avoid recalculating multiple times
                                    $cacheKey = 'member_' . $memberId . '_monthlyPV';
                                    $qualified = Cache::remember($cacheKey, 60, function() use ($memberId) {
                                        return $this->hasMetMonthlyPV($memberId);
                                    });
    
                                    if ($qualified) {
                                        $balancedLeg = $level["balanced_leg"];
                                        $getMemberPoints = $upline->getLesserLegEarnings('Points');
                                        $getMemberLesserLeg = $getMemberPoints["lesserleg"];
    
                                        if (strtolower($getMemberLesserLeg) == strtolower($balancedLeg)) {
                                            $desc = "Team Bonus Earnings From Level $Lkey - $member->username";
                                            $checkEarnings = $this->hasExceededCap($upline->id, $package->id);
    
                                            if (!$checkEarnings) {
                                                $Cash = $this->getTeamBonusCash($mpackage, $package);
    
                                                if ($Cash > 0) {
                                                    // Apply the 50th-level bonus adjustment
                                                    if ($Lkey % 50 == 0) {
                                                        $Cash /= 2;
                                                    }
    
                                                    // Add earnings to the upline
                                                    $upline->addEarnings($Cash, "Cash", $desc, $member->id, $rand);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
    
            DB::commit(); // Commit the transaction if everything is successful
    
        } catch (\Exception $e) {
            DB::rollback(); // Rollback the transaction if an error occurs
    
    
            // Optionally re-throw the exception to propagate the error
            throw $e;
        }
    }

    
    
    


}
