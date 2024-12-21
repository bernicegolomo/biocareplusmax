<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\Package;
use App\Models\Product;
use App\Models\AccessToken;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\MembersPackage;
use App\Models\Earning;
use App\Models\Debit;
use App\Models\Store;
use App\Models\Order;
use App\Models\Bank;
use App\Models\Pickup;
use App\Models\StockistPackage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\RankThreshold;
use App\Models\RankLevel;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

class MemberController extends Controller
{
    
    public function packages(Request $request)
    {  
        $packages = Package::OrderBy('id', "ASC")->get();
        $title = "Packages";
        return view('front.packages', compact('title', 'packages'));
        
    }


    public function showRegistrationForm($id)
    {
        $title = "BCM Registeration";
        //$countrys = DB::table('countries')->get();

        $countries = Country::all();

        //foreach ($countrys as $country) {
            //$currency = DB::table('currencies')
                //->whereJsonContains('country', (string) $country->id)
                //->first();

            //if ($currency) {
                //$countries[] = $country; // Check if it returns any result
            //}
        //}

        $package = Package::find($id);
        return view('auth.register', compact('title', 'id', 'countries', 'package'));
    }

    public function registerdownline($id){
        $title = "BCM Registeration";
        //$countrys = DB::table('countries')->get();
        $countries = Country::all();
        $user = Auth::guard('web')->user();

        //foreach ($countrys as $country) {
            //$currency = DB::table('currencies')
                //->whereJsonContains('country', (string) $country->id)
                //->first();

            //if ($currency) {
                //$countries[] = $country; // Check if it returns any result
            //}
        //}

        $members = $user->getTreeMembers();
        $usernames = $members->map(function ($member) {
            return $member->username; // Adjust this if you have a different field for username
        });

        //dd($usernames);
        $package = Package::find($id);
        return view('auth.registerdownline', compact('title', 'id', 'user', 'countries', 'package','usernames'));
    }


    public function register(Request $request)
    {
        $this->validateRequest($request);

        if ($request->has('_token') && isset($request->token) && $request->token == 1) {
            $request->validate([
                'accesstoken' => 'required|string|max:16',
            ]);
        }

        if ($this->isTokenValid($request)) { 
            $validateToken = AccessToken::where('token', $request->accesstoken)
                                        ->where('amount', $request->amount)
                                        ->whereNull('usedby')
                                        ->whereNull('usedfor')
                                        ->whereNull('used_date')
                                        ->first();

                                        
            if (isset($validateToken) && !empty($validateToken)) { 
                DB::beginTransaction();

                try {
                    $member = new Member([
                        'name'      => $request->name,
                        'username'  => $request->username,
                        'email'     => $request->email,
                        'country'   => $request->country,
                        'password'  => Hash::make($request->password),
                    ]);
                    $member->save();
                    
                    if ($request->referrer) {
                        $referrer = Member::where('username', $request->referrer)->first();

                        if ($referrer && !empty($referrer)) {
                            if ($request->placement == 'left') {
                                $this->placeMember($referrer, $member, 'left');
                            } elseif ($request->placement == 'right') {
                                $this->placeMember($referrer, $member, 'right');
                            } else {
                                return back()->with('error', 'Invalid placement option.');
                            }
                    
                            $member->referrer_id = $referrer->id;
                            $member->save();
                        } else {
                            return back()->with('error', 'Referrer not found.');
                        }
                    } else {
                        $referrer = $this->findNextAvailableReferrer();
                        if ($referrer && !empty($referrer)) {
                            $this->placeMember($referrer, $member);
                            $member->referrer_id = $referrer->id;
                            $member->save();
                        }
                    }


                    // Update transaction table & access token
                    $validateToken->usedby = $member->id;
                    $validateToken->usedfor = "Registration";
                    $validateToken->used_date = date("Y-m-d");
                    $validateToken->status = 1;
                    $validateToken->update();

                    

                    //update transaction table
                    $transaction                    = new Transaction();
                    $transaction->member_id         = $member->id;
                    $transaction->type              = "Registration";
                    $transaction->amount            = $request->amount;
                    $transaction->pv                = $request->pv;
                    $transaction->payment_method    = "AccessToken";
                    $transaction->payment_method_id = $validateToken->id;
                    $transaction->status            = 1;
                    $transaction->save();
                    
                    // Update members package table
                    $this->updateMemberPackage($request, $member, $transaction->id);

                    //give free voucher to new member
                    $this->handleVoucher($request, $member);

                    
                    DB::commit();
                    
                    
                    $this->RegistrationEmail($member->email,$member->id);

                    return redirect('dashboard')->with('success', 'Registration successful.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Error occurred during registration: ');
                }
            }

            return back()->with('error', 'Invalid Access Token. If this error persists, please contact the system administrator.');
        }
    }

    public function memberregister(Request $request)
    {
        $this->validateRequest($request);

        $member = Auth::guard('web')->user();
        if (!$member) {
            return back()->with('error', 'Member not found.');
        }

        $balance = $member->getBalance("Voucher");
        $ponsor = "";

        if ($balance >= $request->amount) {
            DB::beginTransaction();

                try {
                    $newmember = new Member([
                        'name'      => $request->name,
                        'username'  => $request->username,
                        'email'     => $request->email,
                        'country'   => $request->country,
                        'password'  => Hash::make($request->password),
                    ]);
                    $newmember->save();
                    
                    if(isset($request->binary) && !empty($request->binary)){
                        $referrer = $request->binary;
                    }else{
                        $referrer = $request->referrer;
                    }
                    
                    //save sponsor if selected 
                    if($request->referrer && !empty($request->referrer)){
                        $sponsor  = Member::where('username', $request->referrer)->first();
                        
                        if (isset($sponsor) && !empty($sponsor)) {
                            $newmember->referrer_id = $sponsor->id;
                        }
                    }
                    
                    
                    if ($referrer && !empty($referrer)) {
                        $referrer = Member::where('username', $referrer)->first();

                        if ($referrer && !empty($referrer)) {
                            if ($request->placement == 'left') {
                                $this->placeMember($referrer, $newmember, 'left');
                            } elseif ($request->placement == 'right') {
                                $this->placeMember($referrer, $newmember, 'right');
                            } else {
                                return back()->with('error', 'Invalid placement option.');
                            }
                    
                            if(!isset($sponsor) OR empty($sponsor)){
                                $newmember->referrer_id = $referrer->id;
                            }
                            $newmember->save();
                        } else {
                            return back()->with('error', 'Referrer not found.');
                        }
                    } else {
                        $referrer = $this->findNextAvailableReferrer();
                        if ($referrer && !empty($referrer)) {
                            $this->placeMember($referrer, $newmember);
                            
                            if(!isset($sponsor) OR empty($sponsor)){
                                $newmember->referrer_id = $referrer->id;
                            }
                            $newmember->save();
                        }
                    }

                    //dd($referrer);
                    $desc = "Registration for " . $request->username;

                    // Add debit to member's voucher
                    $member->addDebit($request->amount, "Voucher", $desc);

                    //update transaction table

                    $transaction                    = new Transaction();
                    $transaction->member_id         = $newmember->id;
                    $transaction->type              = "Registration";
                    $transaction->amount            = $request->amount;
                    $transaction->pv                = $request->pv;
                    $transaction->payment_method    = "Voucher";
                    $transaction->status            = 1;
                    $transaction->save();
                    
                    // Update members package table
                    $this->updateMemberPackage($request, $newmember, $transaction->id);

                    //give free voucher to new member
                    $this->handleVoucher($request, $newmember);
                    
                    DB::commit();
                    $this->RegistrationEmail($newmember->email,$member->id);

                    return redirect('mydownlines')->with('success', 'Registration successful.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    
                    return back()->with('error', 'Error occurred during registration: ' . $e->getMessage());

                }
            
        }

        \Log::error('Insufficient account balance for registration:', ['member_id' => $member->id, 'balance' => $balance]);
        return back()->with('error', 'Insufficient account balance. If this error persists, please contact the system administrator.');
    }

    
    protected function RegistrationEmail($to,$memberid){
        $subject = "Welcome to BCM!";
        
        // Create the HTML message
        $message = '
            <html>
            <head>
                <title>Password Reset Request</title>
                <meta charset="UTF-8">
            </head>
            <body>
                <h1>Hello,</h1>
                <p>We’re excited to have you join our community of entrepreneurs and product enthusiasts!</p>
                <p>To help you get started, join our exclusive WhatsApp group where you can:</p>
                <p>Connect with fellow partners</p>
                <p>Stay updated on product launches and promotions</p>
                <p>Get support whenever you need it</p>
                <p>👉 Join Here: <a href="https://chat.whatsapp.com/HAv1TWZfAOj5sTZzp8h56V"> WhatsApp Link </a></p>
                <p>We’re here to support you—welcome to the BCM family!</p>
                <p>Best Regards,<br>BCM Support Team</p>
            </body>
            </html>';

        
        // Set content-type header for HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // Additional headers
        $headers .= 'From: Biocaremaxplus <info@biocaremaxplus.com>' . "\r\n";
        
        // Send the email
        mail($to, $subject, $message, $headers);
        
        
        //get all uplines
        $currentMember = Member::find($memberid);
    
        while ($currentMember->referrer_id) {
            // Get the upline member
            $upline = Member::find($currentMember->referrer_id);
    
            if($upline && !empty($upline)){
                $subjectu = "New Downline Registration!";
                $tou = $upline->email;
        
                // Create the HTML message
                $messageu = '
                    <html>
                    <head>
                        <title>New Registration</title>
                        <meta charset="UTF-8">
                    </head>
                    <body>
                        <h1>Hello,</h1>
                        <p>Congratulations! A new member has been added to your team</p>
                        <p>Best Regards,<br>BCM Support Team</p>
                    </body>
                    </html>';
                
                // Send the email
                mail($tou, $subjectu, $messageu, $headers);
            }
    
            // Move to the next upline
            $currentMember = $upline;
        }
        
        
    }
    
    
    // Function to validate the incoming request
    protected function validateRequest(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:members',
            'email'     => 'required|string|email|max:255',
            'password'  => 'required|string|min:8|confirmed',
            'placement' => 'nullable|in:left,right',
            'referrer'  => 'nullable|exists:members,username',
            'binary'    => 'nullable',
            'country'   => 'required|integer',
            'package'   => 'required|integer',
            'pv'        => 'required|integer',
            'amount'    => 'required',
            'voucher'   => 'required|integer',
        ]);

        
    }

    // Function to check if token is valid
    protected function isTokenValid(Request $request)
    {
        return $request->has('_token') && isset($request->token) && $request->token == 1;
    }

    // Function to update the access token
    protected function updateAccessToken($validateToken, $member)
    {
        $validateToken->update([
            'usedby'    => $member->id,
            'usedfor'   => 'Registration',
            'used_date' => now(),
            'status'    => 1,
        ]);
    }

    // Function to validate the access token
    protected function validateAccessToken(Request $request)
    {
        return AccessToken::where('token', $request->accesstoken)
            ->where('amount', $request->amount)
            ->whereNull('usedby')
            ->whereNull('usedfor')
            ->whereNull('used_date')
            ->first();
    }

    
    // Function to handle voucher
    protected function handleVoucher(Request $request, $member)
    {
        if (!empty($request->voucher)) {
            $desc = "Registration Free Voucher Bonus";
            $voucher = $request->voucher;
            if(isset($voucher) && !empty($voucher) && $voucher > 0){
                $member->addEarnings($voucher, "Free Voucher", $desc, $member->id);
            }
        }
    }

    // Function to update member package
    protected function updateMemberPackage(Request $request, $member, $transactionid)
    {
        $mPackage                   = new MembersPackage();
        $mPackage->member_id        = $member->id;
        $mPackage->package_id       = $request->package;
        $mPackage->amount           = $request->amount;
        $mPackage->transaction_id   = $transactionid;
        $mPackage->subcribe_date    = now();
        $mPackage->save();
    }

    private function placeMember($referrer, $member, $preferredPlacement = null)
    {
        // Check if referrer is null
        if (!$referrer) {
            Log::error('Referrer is null in placeMember method', [
                'member_id' => $member->id,
                'preferred_placement' => $preferredPlacement,
            ]);
            return; // Exit the method if referrer is null
        }

        if ($preferredPlacement == 'left' && !$referrer->left_leg_id) {
            $referrer->left_leg_id = $member->id;
            $referrer->save();
        } elseif ($preferredPlacement == 'right' && !$referrer->right_leg_id) {
            $referrer->right_leg_id = $member->id;
            $referrer->save();
        } else {
            $placement = $preferredPlacement ?? 'left';
            $this->placeInDownline($referrer, $member, $placement);
        }
    }

    private function placeInDownline($referrer, $member, $placement)
    {
        $nextLeg = ($placement == 'left') ? 'left_leg_id' : 'right_leg_id';

        if (!$referrer->$nextLeg) {
            $referrer->$nextLeg = $member->id;
            $referrer->save();
        } else {
            $nextReferrer = Member::find($referrer->$nextLeg);
            $this->placeInDownline($nextReferrer, $member, $placement);
        }
    }

    private function findNextAvailableReferrer()
    {
        $referrer = Member::whereNull('left_leg_id')
            ->orWhereNull('right_leg_id')
            ->first();

        if ($referrer) {
            return $referrer;
        }

        $referrers = Member::all();
        foreach ($referrers as $ref) {
            if (!$ref->left_leg_id || !$ref->right_leg_id) {
                return $ref;
            }
        }

        return null;
    }
    
    



    
    protected function teamBonusOld($member, $rand)
    {
        // Get the member's uplines and package information
        $memberTrees = $member->getAllUplinesWithCounts($member->id);
        $package = $this->getMemberPackage($member->id);
    
        if (isset($package) && !empty($package)) {
            // Get pairing bonus and caps based on package
            $bonusDetails = $member->getPackageBonusDetails($package);
    
            if (count($memberTrees) > 0) {
                foreach ($memberTrees as $memberId => $memberTree) {
                    $levels = $memberTree['counts'];
                    $upline = Member::find($memberId);
                    
                    // Loop through the levels
                    foreach ($levels as $Lkey => $level) {
                        // Skip first level
                        if ($Lkey != 1 && $member->isEligibleForBonus($upline, $level, $Lkey)) {
                            // Determine smaller leg and check accumulation
                            $smallerLeg = $member->getSmallerLeg($level);
                            if ($level[$smallerLeg] >= 35) {
                                // 50th level adjustment
                                $bonus = $Lkey % 50 == 0 ? $bonusDetails['bonus_per_pair'] * 0.5 : $bonusDetails['bonus_per_pair'];
                                
                                $desc = "Team Bonus Earnings From Level" .' '. $Lkey .' - '. $member->username;
                                $descCompare = "Team Bonus Earnings From Level" .' '. $Lkey;
                                
                                $checkEarnings = Earning::whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($descCompare) . '%'])
                                                        ->where('member_id', $upline->id)
                                                        ->where('type', "Cash")
                                                        ->whereRaw('(SELECT SUM(value) FROM earnings WHERE member_id = ? AND type = "Cash" AND MONTH(created_at) = ? AND YEAR(created_at) = ?) < ?', 
                                                                   [$upline->id, now()->month, now()->year, $bonusDetails['monthly_cap']])
                                                        ->first();
    
                                if (empty($checkEarnings)) {
                                    // Add earnings
                                    $upline->addEarnings($bonus, "Cash", $desc, $member->id, $rand);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    function hasExceededCap($memberId, $packageId) {
        // Define the caps and bonuses for each package
        $packageCaps = [
            '1' => [
                'bonus_per_pair' => 1000,
                'daily_cap' => 10000,
                'weekly_cap' => 70000,
                'monthly_cap' => 280000,
                'yearly_cap' => 3360000,
            ],
            '2' => [
                'bonus_per_pair' => 1200,
                'daily_cap' => 18000,
                'weekly_cap' => 126000,
                'monthly_cap' => 504000,
                'yearly_cap' => 6048000,
            ],
            '3' => [
                'bonus_per_pair' => 1500,
                'daily_cap' => 30000,
                'weekly_cap' => 210000,
                'monthly_cap' => 840000,
                'yearly_cap' => 10080000,
            ],
            '4' => [
                'bonus_per_pair' => 2200,
                'daily_cap' => 55000,
                'weekly_cap' => 385000,
                'monthly_cap' => 1540000,
                'yearly_cap' => 18480000,
            ],
        ];
    
        // Get the package details for the user
        $package = $packageCaps[$packageId] ?? null;
    
        if (!$package) {
            return false; // Invalid package ID
        }
    
        // Get the earnings for the current day, week, month, and year for Team Bonus Earnings
        $dailyEarnings = Earning::where('member_id', $memberId)
            ->where('type', 'Cash')
            ->where('description', 'LIKE', '%Team Bonus Earnings%')
            ->whereDate('created_at', now())
            ->sum('value');
    
        $weeklyEarnings = Earning::where('member_id', $memberId)
            ->where('type', 'Cash')
            ->where('description', 'LIKE', '%Team Bonus Earnings%')
            ->where('created_at', '>=', now()->startOfWeek()) // Adjust based on your week start
            ->sum('value');
    
        $monthlyEarnings = Earning::where('member_id', $memberId)
            ->where('type', 'Cash')
            ->where('description', 'LIKE', '%Team Bonus Earnings%')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('value');
    
        $yearlyEarnings = Earning::where('member_id', $memberId)
            ->where('type', 'Cash')
            ->where('description', 'LIKE', '%Team Bonus Earnings%')
            ->whereYear('created_at', now()->year)
            ->sum('value');
    
        // Check if the user has exceeded their caps
        if (
            $dailyEarnings >= $package['daily_cap'] ||
            $weeklyEarnings >= $package['weekly_cap'] ||
            $monthlyEarnings >= $package['monthly_cap'] ||
            $yearlyEarnings >= $package['yearly_cap']
        ) {
            return true; // Exceeded cap
        }
        
        
    
        return false; // Not exceeded cap
    }





    protected function teamBonusLastWorking($member,$rand){
        $memberTrees = $member->getAllUplinesWithCountss($member->id); 
        //$newMember = $memberTrees[$member->id];
        
        //$memberTrees = $this->getAllUplines($member);
        
        
      
        $mpackage = $this->getMemberPackage($member->id);
        

        if(isset($mpackage) && !empty($mpackage)){ 
            
            

            if(count($memberTrees) > 0){ 
                foreach($memberTrees as $memberId => $memberTree){ 
                    if($memberId != $member->id){ 
                        $package = $this->getMemberPackage($memberId);
                        //$getCash = $package->teamBonus;
                        //$Cash = $getCash;
            
                        $levels = $memberTree['counts'];
                        $upline = Member::find($memberId); //dd($levels);
                        foreach($levels as $Lkey => $level){ 
                            if($Lkey != 1){ 
                            
                                if(isset($levels[1]) && $levels[1]["left"] >= 1 && $levels[1]["right"] >= 1){ 
                                //if($levels[1]["total"] >= 2){ 
                                    $qualified = $this->hasMetMonthlyPV($memberId);
                                    
                                    if($qualified){
                                        $balancedLeg = $level["balanced_leg"];
                                        $getMemberPoints = $upline->getLesserLegEarnings('Points');
                                        $getMemberLesserLeg = $getMemberPoints["lesserleg"];
        
                                        //dd($balancedLeg);
                                        if(strtolower($getMemberLesserLeg) == strtolower($balancedLeg)){ //&& ($getMemberPoints["leftpoint"] >= 35 && $getMemberPoints["rightpoint"] >= 35)){
                                            //check if member has earned on this level before; if not, drop team bouns earnings
                                            $desc = "Team Bonus Earnings From Level" .' '. $Lkey .' - '. $member->username;
                                            $descCompare = "Team Bonus Earnings From Level" .' '. $Lkey;
                                            //$bAmount = "1500000";
                                            
                                            $memberId = $upline->id; 
                                            $packageId = $package->id; 
                                            $checkEarnings = $this->hasExceededCap($memberId, $packageId);
                                            
                                            
                                            if(!$checkEarnings){  
                                                //drop Earnings 
                                                $Cash = $this->getTeamBonusCash($mpackage,$package);
                                               
                                                //if($packageId == 1 && $Cash > 1000){
                                                    //$Cash = 1000;
                                                //}elseif($packageId == 2 && $Cash > 1200){
                                                    //$Cash = 1200;
                                                //}elseif($packageId == 3 && $Cash > 1500){
                                                    //$Cash = 1500;
                                                //}elseif($packageId == 4 && $Cash > 2200){
                                                    //$Cash = 2200;
                                                //}
                                                
                                                
                                                if($Cash > 0){
                                                    if(($Lkey % 50 == 0)){
                                                        $Cash = ($Cash/2);
                                                    }
                                                    $upline->addEarnings($Cash,"Cash",$desc,$member->id,$rand);
                                                    
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
        }
    }
 
 
    public function teamBonus(Member $member, $rand)
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
    
            $mpackage = self::getMemberPackage($member->id);
    
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
                                        return self::hasMetMonthlyPV($memberId);
                                    });
    
                                    if ($qualified) {
                                        $balancedLeg = $level["balanced_leg"];
                                        $getMemberPoints = $upline->getLesserLegEarnings('Points');
                                        $getMemberLesserLeg = $getMemberPoints["lesserleg"];
    
                                        if (strtolower($getMemberLesserLeg) == strtolower($balancedLeg)) {
                                            $desc = "Team Bonus Earnings From Level $Lkey - $member->username";
                                            $checkEarnings = self::hasExceededCap($upline->id, $package->id);
    
                                            if (!$checkEarnings) {
                                                $Cash = self::getTeamBonusCash($mpackage, $package);
    
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




    public function getTeamBonusCash($mpackage,$package){
        $cash = 0;
        if($package->id == 1){
            if($mpackage->id == 1){
                $cash = "500";   
            }elseif($mpackage->id == 2){
                $cash = "1700";   
            }elseif($mpackage->id == 3){
                $cash = "3400";   
            }elseif($mpackage->id == 4){
                $cash = "6500";   
            }
        }elseif($package->id == 2){
            if($mpackage->id == 1){
                $cash = "600";   
            }elseif($mpackage->id == 2){
                $cash = "2000";   
            }elseif($mpackage->id == 3){
                $cash = "4000";   
            }elseif($mpackage->id == 4){
                $cash = "8000";   
            }
        }elseif($package->id == 3){
            if($mpackage->id == 1){
                $cash = "800";   
            }elseif($mpackage->id == 2){
                $cash = "2500";   
            }elseif($mpackage->id == 3){
                $cash = "5000";   
            }elseif($mpackage->id == 4){
                $cash = "10000";   
            }
        }elseif($package->id == 4){
            if($mpackage->id == 1){
                $cash = "1000";   
            }elseif($mpackage->id == 2){
                $cash = "3500";   
            }elseif($mpackage->id == 3){
                $cash = "7000";   
            }elseif($mpackage->id == 4){
                $cash = "14000";   
            }
        }
        
        return $cash;
    }
    

    public function getDownlines($id = null)
    {
        try {
            $member = $id ? Member::findOrFail($id) : Auth::guard('web')->user();
            $downlineTree = $this->buildTree($member);
    
            return response()->json($downlineTree);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve downlines'], 500);
        }
    }
    
    
    public function getDownlineTree($memberId = null)
    {
        try {
            if ($memberId != null) {
                $member = Member::with('leftLeg', 'rightLeg')->find($memberId);
            } else {
                $user = Auth::guard('web')->user();
                $member = Member::with('leftLeg', 'rightLeg')->find($user->id);
            }
    
            if (!$member) {
                return response()->json(['error' => 'Member not found'], 404);
            }
    
            $tree = $this->buildTree($member);
    
            return response()->json($tree);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve downline tree'], 500);
        }
    }
    
    private function buildTree($member)
    {
        $defaultProfilePicture = asset('front/assets/images/profiles/default.jpg');
        $profile = !empty($member->profile_picture)
            ? asset('front/assets/images/profiles/'.$member->profile_picture)
            : $defaultProfilePicture;
    
        $tree = [
            'id'              => $member->id,
            'name'            => $member->name,
            'username'        => $member->username,
            'email'           => $member->email,
            'profile_picture' => $profile,
            'left'            => $member->leftLeg ? $this->buildTree($member->leftLeg) : null,
            'right'           => $member->rightLeg ? $this->buildTree($member->rightLeg) : null,
        ];
    
        return $tree;
    }
    
    
    
    public function listDownlines(Request $request)
    {
        // Define validation rules
        $rules = [
            'rank'      => 'nullable|string|max:255',
            'package'   => 'nullable|integer|exists:packages,id',
            'from'      => 'nullable|date|before_or_equal:to',
            'to'        => 'nullable|date|after_or_equal:from',
            'username'  => 'nullable|string|max:255',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $user = Auth::guard('web')->user();
        $title = "My Downlines";
    
        // Get all downline IDs
        $downlineDetails = $user->getAllDownlineDetails($user->id);
        $downlineIds = $downlineDetails;
    
        // Apply filters to the query for members
        $query = Member::whereIn('id', $downlineIds);
    
        // Filter by username if provided
        $usernameFilter = $request->input('username');
        if (!empty($usernameFilter)) {
            $query->where('username', 'LIKE', "%{$usernameFilter}%");
        }
    
        // Filter by entry date if provided
        $dateFrom = $request->input('from');
        $dateTo = $request->input('to');
        if (!empty($dateFrom) && !empty($dateTo)) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
    
        // Get the filtered members after username and date filtering
        $filteredMembers = $query->get();
        $filteredMemberIds = $filteredMembers->pluck('id')->toArray();
    
        // Fetch all packages for the filtered members (before applying any package filter)
        $packagesQuery = MembersPackage::select('members_packages.*', 'packages.name as package_name')
            ->join('packages', 'members_packages.package_id', '=', 'packages.id')
            ->whereIn('members_packages.member_id', $filteredMemberIds);
    
        // Apply package filter if provided
        $packageFilter = $request->input('package');
        if (!empty($packageFilter)) {
            $packagesQuery->where('packages.id', $packageFilter);
        }
    
        // Get the packages and group them by member_id
        $packages = $packagesQuery->get()->groupBy('member_id');
    
        // If the package filter is applied and no members match, return empty results
        if (!empty($packageFilter) && $packages->isEmpty()) {
            return view('members.listdownlines', [
                'title'         => $title,
                'user'          => $user,
                'paginator'     => new LengthAwarePaginator([], 0, 10),
                'members'       => collect(),
                'packages'      => collect(),
                'newpackages'   => Package::all(),
                'ranks'         => collect(),
                'totalMembers'  => 0,  // No members found after filtering
            ]);
        }
    
        // Adjust the filteredMemberIds if a package filter was applied
        if (!empty($packageFilter)) {
            $filteredMemberIds = array_keys($packages->toArray());
        }
    
        // **Rank Filter Logic**
        $rankFilter = $request->input('rank');
        if (!empty($rankFilter)) {
            // Filter members based on rank
            $filteredMemberIds = collect($filteredMemberIds)->filter(function ($memberId) use ($rankFilter) {
                $rank = MemberController::calculateUserRank($memberId);
                return $rank === $rankFilter; // Only keep members that match the rank
            })->toArray();
        }
    
        // Get the total number of filtered members
        $totalMembers = count($filteredMemberIds); // Count of all members matching filters before pagination
    
        // Paginate the filtered results
        $perPage = 10; // Number of items per page
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($filteredMemberIds, ($currentPage - 1) * $perPage, $perPage);
    
        $paginator = new LengthAwarePaginator(
            $currentItems,
            count($filteredMemberIds),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    
        // Append search parameters to pagination links
        $paginator->appends($request->all());
    
        // Fetch members for the paginated IDs
        $members = Member::whereIn('id', $paginator->items())->get()->keyBy('id');
    
        // Pass all packages to the view
        $newpackages = Package::all();
    
        // Calculate ranks for all members in the paginated results
        $ranks = collect($paginator->items())->mapWithKeys(function ($memberId) {
            $rank = MemberController::calculateUserRank($memberId);
            return [$memberId => $rank];
        });
    
        return view('members.listdownlines', compact('title', 'user', 'paginator', 'members', 'packages', 'newpackages', 'ranks', 'totalMembers'));
    }



    public function mydownlines($id=null){

        $user = Auth::guard('web')->user();
        $title = "My Downlines";
        $referralLink  = "";

        if(isset($user) && !empty($user)){
            $referralLink = url('/register?ref=' . $user->username);
        }

        if($id != NULL){
            
            return view('members.downlines', compact('title', 'user', 'id', 'referralLink'));
        }

        return view('members.downlines', compact('title', 'user', 'referralLink'));
    }

    public function newdownline(){
        $user = Auth::guard('web')->user();
        $title = "New Downline";
        $packages = Package::all();

        return view('members.newdownline', compact('title', 'user', 'packages'));
    }
    
    public function forgotpassword(){
        return view('auth.forgotpassword');
    }
    
    public function passwordreset(Request $request)
    {
        $request->validate(['username' => 'required|string']);

        // Find the user by username
        $user = Member::where('username', $request->username)->first();
        

        if (!$user) {
            return back()->withErrors(['username' => 'No user found with that username.']);
        }
        
        $token = Str::random(60);

        // Insert or update the token in the password_resets table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->username],
            ['email' => $user->username, 'token' => $token, 'created_at' => Carbon::now()]
        );
        
        
        $to = $user->email;
        $subject = 'Password Reset Request';
        
        // Generate the password reset link
        $resetLink = url(route('password.reset', ['token' => $token, 'email' => $user->username], false));

        
        // Create the HTML message
        $message = '
        <html>
        <head>
            <title>Password Reset Request</title>
        </head>
        <body>
            <h1>Hello,</h1>
            <p>We received a request to reset your password. If you did not make this request, please ignore this email.</p>
            <p>To reset your password, click the link below:</p>
            <p><a href="' . $resetLink . '" style="color: #4CAF50; text-decoration: none; font-weight: bold;">Reset Your Password</a></p>
            <p>If the above link does not work, copy and paste the following URL into your browser:</p>
            <p><a href="' . $resetLink . '">' . $resetLink . '</a></p>
            <p>Best Regards,<br>BCM Support Team</p>
        </body>
        </html>';
        
        // Set content-type header for HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // Additional headers
        $headers .= 'From: Biocaremaxplus <info@biocaremaxplus.com>' . "\r\n";
        
        // Send the email
        if (mail($to, $subject, $message, $headers)) {
            return back()->with('success', 'Password reset link sent to your email.');
        } else {
            return back()->with('error', 'Failed to send email.');
        }
        
    }
    
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:members,username',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);
    
        // Find the reset token
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->username)
            ->where('token', $request->token)
            ->first();
    
        if (!$passwordReset) {
            return back()->withErrors(['username' => 'Invalid password reset token or username.']);
        }
    
        // Find the user
        $user = Member::where('username', $request->username)->first();
        if (!$user) {
            return back()->withErrors(['username' => 'User does not exist.']);
        }
    
        // Update the password
        $user->password = Hash::make($request->password);
        $user->save();
    
        // Delete the token after successful password reset
        DB::table('password_reset_tokens')->where('email', $request->username)->delete();
    
        // Redirect to login with success message
        return redirect('/login')->with('success', 'Your password has been successfully updated!');
    }
    
    
    public function showResetForm()
    {
        return view('auth.passwords.reset');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username'  => 'required|string|max:30',
            'password'  => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        //dd($credentials);

        if (Auth::guard('web')->attempt($credentials, $request->remember)) { 
            //$request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->with('error','The provided credentials do not match our records.');
    }

    public function dashboard(){  
        $user = Auth::guard('web')->user(); 
        $checkOrder = $checkROrder = "";
        //$this->settleMonthlyTeamBonus();
        
        if($user){  
            //check if 1st purchase from entry store has been made
            $checkEntryStore = Transaction::where('type', "Registration")
                                        ->where('member_id',$user->id)
                                        ->where('amount', '>', 0)
                                        ->orderby('id', "DESC")
                                        ->first();


            if(($checkEntryStore && !empty($checkEntryStore))){
                $checkROrder = Transaction::where('type', "1")
                                        ->where('member_id', $user->id)
                                        ->where('created_at', '>', $checkEntryStore->created_at)
                                        ->first();
                                        
            }

            //check if 1st purchase from entry store has been made after upgrade
            $checkEntryStoreUpgrade = Transaction::where('type', "Package Update")
                                        ->where('member_id',$user->id)
                                        ->where('amount', '>', 0)
                                        ->orderby('id', "DESC")
                                        ->first();


            if(($checkEntryStoreUpgrade && !empty($checkEntryStoreUpgrade))){
                $checkOrder = Transaction::where('type', "1")
                                        ->where('member_id', $user->id)
                                        ->where('created_at', '>', $checkEntryStoreUpgrade->created_at)
                                        ->first();
                                        
            }
            
            
           
            
            if( ( empty($checkOrder) && !empty($checkEntryStoreUpgrade) ) OR  (empty($checkROrder)) && !empty($checkEntryStore) ){ 
                
                //redirect to entry store
                $title = "Entry Store";
                $products = Product::whereJsonContains('store', '1')
                                        ->whereNotNull('image')
                                        ->orderBy('id', 'DESC')
                                        ->paginate(10);
    
                
                if(empty($checkOrder) && !empty($checkEntryStoreUpgrade)){ $type = "2"; }else{ $type = "1"; }
                
                
                
                return view('members.entrystore', compact('title', 'user', 'products', 'type'));
                
                
            }
            
            
                       
            
            //redirect to dashboard
                $title = "Dashboard";
                $orderCount = Order::where('member_id', $user->id)->count();
                $leftCount = $user->countLeftDescendants();
                $rightCount = $user->countRightDescendants();
                $currentPackage = $this->getMemberPackage($user->id);
                $cash = $user->getBalance("Cash");
                $voucher = $user->getBalance("Voucher");
                $rank = $this->calculateUserRank($user->id);
                
                //dd($leftCount, $rightCount);
            
                return view('members.dashboard', compact('title', 'user', 'orderCount', 'leftCount', 'rightCount', 'currentPackage', 'cash', 'voucher', 'rank'));
    


            

            
        }
        return back()->with('error', 'Access denied.');
    }
    
    
    public function myprofile(){  
        $user = Auth::guard('web')->user(); 
        $title  = $user->name;
        $countries = Country::all();
        $banks = Bank::all();
        
        
        return view('members.myprofile', compact('title', 'user', 'countries','banks'));
    }
    
    public function updatemyprofile(Request $request){ 
        
        $request->validate([
            'id'            => 'required|integer',
            'name'          => 'required|string|max:255',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // restrict to jpg, jpeg, png with max size 2048KB
            'phone'         => 'required',
            'email'         => 'required|string',
            'country'       => 'required|integer',
            'account'       => 'required',
            'bankname'      => 'required|integer',
            'address'       => 'nullable|string',
            'password'      => 'nullable|string',
        ]);

        $user = Member::find($request->id); 
        
        if($user && !empty($user)){
            $user->name         = $request->name;
            $user->phone        = $request->phone;
            $user->email        = $request->email;
            $user->country      = $request->country;
            $user->address      = $request->address;
            $user->bankname     = $request->bankname;
            $user->bankaccount  = $request->account;
            
            
            //update password if it is set
            if(isset($request->password) && !empty($request->password)){
                $user->password  = Hash::make($request->password);
            }
            
            
            //update profile image if it is set
            if($request->hasFile('image')){
                $rand = $this->RandomString(8);
                $filename = $rand.'.'.$request->image->extension();
                $request->image->move('front/assets/images', $filename);
                $user->profile_picture = $filename;
            }    
            
            $user->update();
            
            return back()->with('success', 'Profile has been updated successfully.');
            
        }
        return back()->with('error', 'Error updating profile.');
        
    }


    public function stores(){ 
        $user = Auth::guard('web')->user(); 
        $title = "Stores";

        if($user){
            
            $stores = Store::where('id', "!=", 1)
                            ->OrderBy('id',"ASC")
                            ->get();

            if($stores && count($stores) > 0){ 
                //redirect to dashboard
                $title = "Stores";
                return view('members.stores', compact('title', 'user', 'stores'));
            }
            
        }
        return back()->with('error', 'Access denied.');
    }

    public function store(Request $request, $id=null, $catid=null){ 
        $user = Auth::guard('web')->user();
        
        
        if($user){
            if($id != null && $catid == null){
                //check if cart is not empty then clear
                $cart = session()->get('cart', []);
                $store = Store::where('id', $id)->first();
                
    
                if (!empty($cart)) { 
                    foreach ($cart as $item) {
                        $storeid = $item['store'];
                        if($storeid != $store->id){
                            // Clear the cart
                            session()->forget('cart');
                        }
                    }
                    
                }
    
                
    
                if($store && !empty($store)){ 
                    //redirect to dashboard
                    $title = $store->name;
                    $products = Product::whereJsonContains('store', $id)
                                        ->whereNotNull('image')
                                        ->orderBy('id', 'DESC')
                                        ->paginate(10);
    
    
                    return view('members.store', compact('title', 'user', 'store', 'products', 'id'));
                }
                
            }elseif($id != null && $catid != null){
                //check if cart is not empty then clear
                $cart = session()->get('cart', []);
                $store = Store::where('id', $id)->first();
                
    
                if (!empty($cart)) { 
                    foreach ($cart as $item) {
                        $storeid = $item['store'];
                        if($storeid != $store->id){
                            // Clear the cart
                            session()->forget('cart');
                        }
                    }
                    
                }
    
                $cat = Crypt::decrypt($cat);
                if($store && !empty($store)){ 
                    //redirect to dashboard
                    $title = $store->name;
                    $products = Product::whereJsonContains('store', $id)
                            ->whereNotNull('image')
                            ->where('category_id',  $cat)
                            ->orderBy('id', 'DESC')
                            ->paginate(10);
            
                    return view('members.store', compact('title', 'user', 'store', 'products', 'id'));
                }
                
            }else{
                $request->validate([
                    'q'     => 'required|string|max:255',
                    'cat'   => 'nullable|string|max:255',
                ]);
                
                $id = "3";
                $store = Store::where('id', $id)->first();
                $cart = session()->get('cart', []);
                $store = Store::where('id', $id)->first();
                
    
                if (!empty($cart)) { 
                    foreach ($cart as $item) {
                        $storeid = $item['store'];
                        if($storeid != $store->id){
                            // Clear the cart
                            session()->forget('cart');
                        }
                    }
                    
                }
                
                $title = $store->name;
                if(empty($request->cat)){ 
                    $products = Product::whereJsonContains('store', $id)
                                        ->whereNotNull('image')
                                        ->where('name', 'LIKE', '%' . $request->q . '%')
                                        ->orderBy('id', 'DESC')
                                        ->paginate(10);
                }else{
                    $products = Product::whereJsonContains('store', $id)
                                        ->whereNotNull('image')
                                        ->where('name', 'LIKE', '%' . $request->q . '%')
                                        ->Orwhere('category_id',  $request->cat)
                                        ->orderBy('id', 'DESC')
                                        ->paginate(10);
                }
                
                return view('members.store', compact('title', 'user', 'store', 'products', 'id'));
                
                
            }

            return back()->with('error', 'Invalid store selection.');

            
        }
        
        return redirect('/login')->with('error', 'Access denied.');
    }
    
    public function allproducts($cat){
        $user = Auth::guard('web')->user();
        $cat = Crypt::decrypt($cat);
        $id = "3";
        $store = Store::where('id', $id)->first();
        $title = $store->name;
        
        if($user){
        
            $products = Product::whereJsonContains('store', $id)
                            ->whereNotNull('image')
                            ->where('category_id',  $cat)
                            ->orderBy('id', 'DESC')
                            ->paginate(10);
            
            return view('members.store', compact('title', 'user', 'store', 'products', 'id'));
            
        }
        
        return redirect('/login')->with('error', 'Access denied.');
    }



    public function addToCart(Request $request)
    {
        $user = Auth::guard('web')->user(); 

        $currentUrl = url()->current();
        
        $product = Product::find($request->id);
        
        if(isset($request->type) && !empty($request->type)){ $type = $request->type; }else{ $type = "";}

        if ($product) {
            $cart = session()->get('cart', []);
            
            // If item exists in cart, increment quantity
            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] += $request->quantity;
            } else {
                $amount = $product->price;
                $conversion = html_entity_decode("&#8358;");
                
                
                $cart[$product->id] = [
                    'name'      => $product->name,
                    'quantity'  => $request->quantity,
                    'price'     => $amount,
                    'pv'        => $product->pv,
                    'symbol'    => $conversion,
                    'image'     => $product->image,
                    'store'     => $request->store,
                    'type'      => $type,
                    'discount'  => $product->discount,
                ];
            }

            session()->put('cart', $cart);

            return response()->json(['success' => true, 'message' => 'Item added to cart']);
        }

        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }

    public function updateCart(Request $request)
    {
        // Get the cart from session or initialize it if it's empty
        $cart = session()->get('cart', []);
        
        // Get the product ID and new quantity from the request
        $productId = $request->input('id');
        $quantity = $request->input('quantity');
        
        // Check if the product exists in the cart
        if (isset($cart[$productId])) {
            // Update the product quantity in the cart
            $cart[$productId]['quantity'] = $quantity;
        } else {
            return response()->json(['success' => false, 'message' => 'Item not found in cart.']);
        }
    
        // Update the cart back to the session
        session()->put('cart', $cart);
    
        // Recalculate totals (total, discount, and final total)
        $total = 0;
        $discount = 0;
        
        foreach ($cart as $id => $item) {
            $total += $item['quantity'] * $item['price'];
            $discount += $item['quantity'] * $item['discount'];
        }
    
        // Prepare the response to send back to the frontend
        $response = [
            'success' => true,
            'cartItems' => $cart,
            'cartSummary' => [
                'total' => $total,
                'discount' => $discount,
                'cartTotal' => $total - $discount,
                'symbol' => isset($cart[$productId]['symbol']) ? $cart[$productId]['symbol'] : '$',  // Fallback to '$' if symbol is not set
            ],
        ];
    
        return response()->json($response);
    }
    
    public function removeCartItem(Request $request)
    {
        // Get the cart from the session
        $cart = session()->get('cart', []);
    
        // Get the product ID from the request
        $productId = $request->input('id');
    
        // Check if the product exists in the cart
        if (isset($cart[$productId])) {
            // Get the symbol of the item being removed
            $symbol = $cart[$productId]['symbol'];
    
            // Remove the item from the cart
            unset($cart[$productId]);
    
            // Update the cart back to the session
            session()->put('cart', $cart);
    
            // Recalculate the totals
            $total = 0;
            $discount = 0;
    
            foreach ($cart as $id => $item) {
                $total += $item['quantity'] * $item['price'];
                $discount += $item['quantity'] * $item['discount'];
            }
    
            // Prepare the response with updated cart
            $response = [
                'success' => true,
                'cartItems' => $cart,
                'cartSummary' => [
                    'total' => $total,
                    'discount' => $discount,
                    'cartTotal' => $total - $discount,
                    'symbol' => $symbol,  // Use the symbol from the cart item
                ],
            ];
    
            return response()->json($response);
        }
    
        // If item not found in cart, return an error
        return response()->json(['success' => false, 'message' => 'Item not found in cart.']);
    }





    public function getCart()
    {
        $cartItems = session()->get('cart', []);
        $count = count($cartItems);

        // Generate HTML for cart content
        $html = view('components.cartSession', ['cartItems' => $cartItems])->render();

        return response()->json(['count' => $count, 'html' => $html]);
    }

   

public function cartIndex()
{
    // Get the authenticated user
    $user = Auth::guard('web')->user();
    
    // Get the cart from session
    $cart = session()->get('cart', []);

    // Initialize variables for total and discount
    $total = 0;
    $discount = 0;
    
    // Calculate the total and discount if there are items in the cart
    foreach ($cart as $cartitem) {
        $total += $cartitem['quantity'] * $cartitem['price'];
        $discount += $cartitem['quantity'] * $cartitem['discount'];
    }

    // Calculate the cart total (total minus discount)
    $cartTotal = $total - $discount;

    // Get pickup options
    $pickups = Pickup::orderby('type', "ASC")->where('status', 1)->get();
    
    // Return the view with calculated values
    return view('members.viewcart', [
        'cartItems' => $cart,
        'user' => $user,
        'pickups' => $pickups,
        'total' => $total,
        'discount' => $discount,
        'cartTotal' => $cartTotal, // Pass the calculated cart total
        'symbol' => $cart['symbol'] ?? '$', // Assuming the currency symbol
    ]);
}

    public function removeFromCart(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');

            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }

            $total = $this->calculateCartTotal($cart);

            return response()->json(['success' => true, 'cart' => $cart, 'total' => $total]);
        }

        return response()->json(['success' => false]);
    }

    private function calculateCartTotal($cart)
    {
        $total = 0;
        foreach($cart as $item) {
            $total += $item['quantity'] * $item['price'];
        }
        return $total;
    }

    private function getCartItems($cart)
    {
        $total = 0; $store = ""; $items = [];
        foreach($cart as $id => $item) {
            $total += $item['quantity'] * $item['price'];
            $store = $item['store'];
            $type = $item['type'];
            $items[] = $id;
        }
        return @array('total' => $total, 'store' => $store, 'type' => $type, 'items' => $items);
    }

    public function checkoutOLD(Request $request){ 
        
         // Validate that the 'pickup' radio button is required
        $request->validate([
            'pickup' => 'required',
        ], [
            'pickup.required' => 'Please select a pickup option.', // Custom error message
        ]);


        $cartItems = session()->get('cart', []);
        $count = count($cartItems);
        $carts = $this->getCartItems($cartItems);
        $cartTotal = $carts['total'];
        $data = []; $method = "";
        $rand = Str::random(60);

        if($count > 0){
            $member = Auth::guard('web')->user();

            if($carts['store'] == 1 && !isset($cartItems["6"]) && $carts['type'] == 1){ 
                return back()->with('error', 'You must select Face Cap and Brochure');
            }
            
            $fundsCheck = $this->availableFund($member,$carts['store'],$cartTotal,$request,$cartItems,$carts['type'],$rand);
            if($fundsCheck){
                if($carts['store'] == 3){$method = "Voucher & Free Voucher"; }else{$method == "Voucher"; }
                try { 
                    // Save transactions, save order and order items 
                    $transaction =  new Transaction([
                                        'member_id'         => $member->id,
                                        'type'              => $carts["store"],
                                        'amount'            => $cartTotal,
                                        'pv'                => "",
                                        'payment_method'    => $method,
                                        'status'            => 1,
                                        'check_id'          => $rand,
                                    ]);

                        $transaction->save();

                    //save items in order table
                    if($cartItems && count($cartItems)){
                        $totalPV = 0;
                        foreach($cartItems as $cartKey => $cart){
                            $data[] = @array('id'=> $cartKey, 'name' => $cart["name"], 'quantity' => $cart["quantity"], 'price' => $cart["price"]);

                            if($carts["store"] == 2){ 
                                $product = Product::find($cartKey);
                                if ($product) {
                                    // Calculate total PV for this item (quantity * product PV)
                                    $totalPV += $cart['quantity'] * $product->pv;
                                }
                            }
                        }
                        
                        
                        if($request->token == 1){

                            //Give instant 10% cash back
                            if(isset($totalPV) && $totalPV > 0 && $carts["store"] == 2){ 
                                $cashback = (0.05 * $totalPV) * 500;
                                $desc  = "10% instant cash back on product puchased from Re-purchase store";
                                $member->addEarnings($cashback, "Cash", $desc, $member->id,$rand);
                                
                                
                                //Give member sponsor 5% instant chash back
                                $my_Sponsor = $member->referrer_id;
                                if(isset($my_Sponsor) && !empty($my_Sponsor)){
                                    $mySponsor = Member::find($my_Sponsor);
                                    $Scashback = (0.025 * $totalPV) * 500;
                                    $Sdesc  = "5% instant cash back on product puchased from Re-purchase store by". $member->username;
                                    $mySponsor->addEarnings($Scashback, "Cash", $Sdesc, $member->id,$rand);
                                }
                            }
                            
                            
                        }
                        
                        
                                
                        $items = json_encode($data);
                        $order = new Order([
                                    'member_id'         => $member->id,
                                    'store'             => $cart["store"],
                                    'items'             => $items,
                                    'total'             => $cartTotal,
                                    'pickup_id'         => $request->pickup,
                                    'transaction_id'    => $transaction->id,
                                    'status'            => 1,
                                ]);

                        $order->save();
                                
                    }
                            
                    // Clear the cart session
                    $request->session()->forget('cart');
            
                    DB::commit();
                    return back()->with('success', 'Transaction successful: ');
                            
                } catch (\Exception $e) {
                    DB::rollback();
                    return back()->with('error', 'Transaction failed: ');
                }

            
            }else{
                return back()->with('error', 'Insufficient funds: ' );
                
            }
            

                        
        }

    }
    
    public function checkout(Request $request){ 
        

        // Start a database transaction
        DB::beginTransaction();
    
        try {
            // Validate that the 'pickup' radio button is required
            $request->validate([
                'pickup' => 'required',
            ], [
                'pickup.required' => 'Please select a pickup option.', // Custom error message
            ]);
            
    
            $cartItems = session()->get('cart', []);
            $count = count($cartItems);
            $carts = $this->getCartItems($cartItems);
            $cartTotal = $carts['total'];
            $data = []; 
            $method = "";
            $rand = Str::random(60);
            $settingsM = Setting::all()->keyBy('id');
    
            if($count > 0){
                $member = Auth::guard('web')->user();
    
                if($carts['store'] == 1 && !isset($cartItems["6"]) && $carts['type'] == 1){ 
                    return back()->with('error', 'You must select Face Cap and Brochure');
                }
    
                // Call availableFund function within the same transaction
                $fundsCheck = $this->availableFund($member, $carts['store'], $cartTotal, $request, $settingsM, $cartItems, $carts['type'], $rand);
    
                if ($fundsCheck) {
                    if ($carts['store'] == 3) {
                        $method = "Voucher & Free Voucher"; 
                    } else {
                        $method = "Voucher"; 
                    }
    
                    // Save transaction, order, and order items
                    $transaction = new Transaction([
                        'member_id'         => $member->id,
                        'type'              => $carts['store'],
                        'amount'            => $cartTotal,
                        'pv'                => "",
                        'payment_method'    => $method,
                        'status'            => 1,
                        'check_id'          => $rand,
                    ]);
    
                    $transaction->save();
    
                    // Save items in the order table
                    if ($cartItems && count($cartItems)) {
                        $totalPV = 0;
                        foreach ($cartItems as $cartKey => $cart) {
                            $data[] = @array('id' => $cartKey, 'name' => $cart["name"], 'quantity' => $cart["quantity"], 'price' => $cart["price"]);
    
                            if ($carts['store'] == 2) { 
                                $product = Product::find($cartKey);
                                if ($product) {
                                    // Calculate total PV for this item (quantity * product PV)
                                    $totalPV += $cart['quantity'] * $product->pv;
                                }
                            }
                        }
    
                        if ($request->token == 1) {
                            // Give instant 10% cash back
                            if (isset($totalPV) && $totalPV > 0 && $carts["store"] == 2) { 
                                if($settingsM[14]->content > 0){
                                    $cash10 = $settingsM[14]->content;
                                    $cashback = ($cash10 * $totalPV) * 500;
                                    $desc = "10% instant cash back on product purchased from Re-purchase store";
                                    $member->addEarnings($cashback, "Cash", $desc, $member->id, $rand);
                                }
    
                                // Give member sponsor 5% instant cashback
                                $my_Sponsor = $member->referrer_id;
                                if (isset($my_Sponsor) && !empty($my_Sponsor) && $settingsM[15]->content > 0) {
                                    $mySponsor = Member::find($my_Sponsor);
                                    $cash5 = $settingsM[15]->content;
                                    $Scashback = ($cash5 * $totalPV) * 500;
                                    $Sdesc = "5% instant cash back on product purchased from Re-purchase store by " . $member->username;
                                    $mySponsor->addEarnings($Scashback, "Cash", $Sdesc, $member->id, $rand);
                                }
                            }
                        }
    
                        $items = json_encode($data);
                        $order = new Order([
                            'member_id'         => $member->id,
                            'store'             => $cart["store"],
                            'items'             => $items,
                            'total'             => $cartTotal,
                            'pickup_id'         => $request->pickup,
                            'transaction_id'    => $transaction->id,
                            'status'            => 1,
                        ]);
    
                        $order->save();
                    }
    
                    // Clear the cart session
                    $request->session()->forget('cart');
    
                    // Commit the transaction if everything is successful
                    DB::commit();
                    return back()->with('success', 'Transaction successful: ');
    
                } else {
                    // Rollback the transaction if availableFund returns false
                    DB::rollback();
                    return back()->with('error', 'Insufficient funds:');
                }
            }
        } catch (\Exception $e) {
            // Rollback the transaction if there's an exception
            DB::rollback();
            return back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    protected function availableFund($member, $store, $cartTotal, $request, $settingsM, $cartItems = null, $type = null, $rand)
    {
        DB::beginTransaction(); // Start the transaction
    
        try {
            if ($store == 1) {
                $productPV = 0;
    
                if ($type != null && $type == 1) {
                    $TransactionType = "Registration";
                } elseif ($type != null && $type == 2) {
                    $TransactionType = "Package Update";
                }
    
                // Get member's package Entry Store Purchase
                $package = Transaction::where('member_id', $member->id)
                    ->where('type', $TransactionType)
                    ->OrderBy('id', "DESC")
                    ->first();
                    
                $mainPackage = $this->getMemberPackage($member->id);
    
                foreach ($cartItems as $items) {
                    $productPV = $productPV + ($items["pv"] * $items["quantity"]);
                }
    
                $sponsor = Member::where('id', $member->referrer_id)->first();
    
                if (isset($package) && !empty($package)) {
                    if ($productPV <= $package->pv) {
                        $pv = $productPV;
                    } else {
                        $pv = $package->pv;
                    }
    
                    $amount = $package->amount;
                    
                    
    
                    // Check if total cart is <= amount
                    if ($cartTotal <= $amount) { 
    
                        // Distribute points to the upline members (Bonus PV (Points))
                        $bonuspvs = $mainPackage->bonus_pv;
                        $bonusPv = ($pv * $bonuspvs);
                        $desc = "Bonus PV Earnings from " . $member->username;
                        $this->distributePoints($member, $bonusPv, $desc, $pv, $store, $rand);
    
                        // Give direct referral bonus
                        $Ddesc = "Direct Referral Bonus From " . $member->username;
                        $this->distributeReferralBonus($sponsor, $pv, $Ddesc, $member->id, $rand, $settingsM);
    
                            
                        if ($type != null && $type == 1) { 
                            // Call the method to distribute the indirect referral bonus
                            $desc = "Indirect Referral Bonus From " . $member->username;
                            $this->distributeDirectReferralBonus($member, $pv, $desc, $rand);
                            
    
                            // Give team pairing bonus
                            //$this->teamBonus($member, $rand);
                            DB::table('team_bonus_requests')->insert([
                                'member_id' => $member->id,
                                'rand' => $rand,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                        
                        
    
                        // Drop stockist registration bonus
                        $this->stockistRegistrationBonus($request->pickup, $rand);
    
                        // Cashback to stockist
                        $this->stockistCashBack($request->pickup, $cartTotal, "", $rand);
    
                        DB::commit(); // Commit the transaction
                        return true;
                    } else {
                        // Handling the case when total cart is greater than amount
                        $cashBalance = $member->getBalance("Voucher");
                        $totalBalance = ($amount + $cashBalance);
    
                        if ($cashBalance > 0 && $totalBalance >= $cartTotal) {
                            // Deduct cash and return true
                            $cash = ($cartTotal - $amount);
                            $desc = "Payment for Product";
    
                            // Add debit to member's voucher
                            $member->addDebit($cash, "Voucher", $desc, $rand);
    
                            // Distribute points to the upline members (Bonus PV (Points))
                            $bonuspvs = $mainPackage->bonus_pv;
                            $bonusPv = ($pv * $bonuspvs);
                            $desc = "Bonus PV Earnings from " . $member->username;
                            $this->distributePoints($member, $bonusPv, $desc, $pv, $store, $rand);
    
                            // Give direct referral bonus
                            $Ddesc = "Direct Referral Bonus From " . $member->username;
                            $this->distributeReferralBonus($sponsor, $pv, $Ddesc, $member->id, $rand, $settingsM);
    
                            if ($type != null && $type == 1) {
                                // Give team pairing bonus
                                //$this->teamBonus($member, $rand);
                                DB::table('team_bonus_requests')->insert([
                                    'member_id' => $member->id,
                                    'rand' => $rand,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
    
                            // Drop stockist registration bonus
                            $this->stockistRegistrationBonus($request->pickup, $rand);
   
                            // Cashback to stockist
                            $this->stockistCashBack($request->pickup, $cartTotal, "", $rand);
    
                            DB::commit(); // Commit the transaction
                            return true;
                        }
    
                        DB::rollBack(); // Rollback the transaction
                        return false;
                    }
                }
            } elseif ($store == 2) {
                $pv = 0;
    
                if ($request->token2 == 2 && $this->isStockist($member->id)) {
                    $cashBalance = $member->getBalance("Stockist Voucher");
                } else {
                    $cashBalance = $member->getBalance("Voucher");
                }
    
                if ($cashBalance >= $cartTotal) {
                    // Deduct cash and return true
                    $desc = "Payment for Product";
    
                    foreach ($cartItems as $items) {
                        $pv = $pv + ($items["pv"] * $items["quantity"]);
                    }
    
                    if ($request->token2 == 2 && $this->isStockist($member->id)) {
                        
                        $descL = "Restock Commission From ". $member->username;
                        //$stockistL = Member::select('members.*', 'pickups.*')
                                            //->join('pickups', 'pickups.member_id', '=', 'members.id')
                                            //->where('members.id', $member->id)
                                            //->where('pickups.status', 1)
                                            //->first();
                                            
                        $stockistL = Member::select('members.*', 'pickups.*', 'stockist_packages.*')
                                            ->join('pickups', 'pickups.member_id', '=', 'members.id')  
                                            ->join('stockist_packages', 'stockist_packages.id', '=', 'pickups.type')  
                                            ->where('members.id', $member->id)
                                            ->where('pickups.status', 1)
                                            ->first();
                                            
                                            
                                            
                        if($stockistL && !empty($stockistL) && $stockistL->restock_commission > 0)   {  
                            $stockistR = $stockistL->restock_commission; 
                            $stockistCash = ($stockistR * $pv * 500);
                            $member->addEarnings($stockistCash, "Cash", $descL, $member->id, $rand);
                          
                        }
                        
                        if(!empty($member->referrer_id)){
                            
                            //member (depot) earns 0.02 * $stockistCash
                            $descR = "Stockist Sponsor Commission From". $member->username;
                            
                            $stockistReferral = Member::find($member->referrer_id);
                            
                            if(isset($stockistReferral) && $stockistL->sponsor_commission > 0){ 
                                $stockupline = $stockistL->sponsor_commission;
                                $stockistRCash = ($stockupline * $stockistCash);
                                $stockistReferral->addEarnings($stockistRCash, "Cash", $descR,$member->id,$rand);
                            }
                        }
                        
                        
                        //pickup location earns 0.03 * point * 500
                        $pickupL = Member::join('pickups', 'pickups.member_id', '=', 'members.id')
                                        ->where('pickups.id', $request->pickup)
                                        ->where('pickups.status', 1)
                                        ->first();
                        
                                        
                        //$pickupLM  = Member::find($pickupL->member_id); 
                        
                        $pickupLM  = Member::select('members.*', 'pickups.*', 'stockist_packages.*')
                                            ->join('pickups', 'pickups.member_id', '=', 'members.id')  
                                            ->join('stockist_packages', 'stockist_packages.id', '=', 'pickups.type')  
                                            ->where('members.id', $pickupL->member_id) 
                                            ->where('pickups.id', $pickupL->id)
                                            ->where('pickups.status', 1)
                                            ->first();
                                            
                        if(isset($pickupLM) && $pickupLM->pickup_restock_commission > 0){            
                            $pickupLMC = $pickupLM->pickup_restock_commission	;
                            $cashL =  ($pickupLMC * $pv * 500);
                            $pickupLM->addEarnings($cashL, "Cash", $descL,$member->id,$rand);
                        }
                        
                        
                        $member->addDebit($cartTotal, "Stockist Voucher", $desc, $rand);
                    } else {
                        $member->addDebit($cartTotal, "Voucher", $desc, $rand);
                    }
    
                    if ($request->token == 1) {
                        // Distribute points to the upline members (Bonus PV (Points))
                        $Pdesc = "Bonus PV Earnings from " . $member->username;
                        $this->distributePoints($member, "", $Pdesc, $pv, $store, $rand);
    
                        // Cashback to stockist
                        $this->stockistCashBack($request->pickup, $cartTotal, "", $rand);
                    }
                    
                    
    
                    DB::commit(); // Commit the transaction
                    return true;
                }
    
                DB::rollBack(); // Rollback the transaction
                return false;
            } elseif ($store == 3) {
                $deductVoucher = $discount = $pv = $deductCash = 0;
    
                if ($request->token2 == 2 && $this->isStockist($member->id)) {
                    $cashBalance = $member->getBalance("Stockist Voucher");
                    $voucherBalance = $member->getBalance("Stockist Free Voucher");
                    
                    
                } else {
                    $cashBalance = $member->getBalance("Voucher");
                    $voucherBalance = $member->getBalance("Free Voucher");
                }
    
                $cartItems = session()->get('cart', []);
    
                foreach ($cartItems as $items) {
                    $discount = ($items["discount"] * $items["quantity"]) + $discount;
                    $pv = $pv + ($items["pv"] * $items["quantity"]);
                }
    
                $deductVoucher = $discount;
                $deductCash = $cartTotal - $deductVoucher;
                $totalBalance = $cashBalance + $voucherBalance;
    
    
    
                if ($totalBalance >= $cartTotal && $voucherBalance >= $deductVoucher && $cashBalance >= $deductCash) { 
                    // Deduct cash and return true
                    $desc = "Payment for Product";
    
                    if ($discount > 0) { 
                        $deductVoucher = $discount;
    
                        // Add debit to member's voucher
                        if ($request->token2 == 2 && $this->isStockist($member->id)) { 
                            $member->addDebit($deductVoucher, "Stockist Free Voucher", $desc, $rand);
                        } else {
                            $member->addDebit($deductVoucher, "Free Voucher", $desc, $rand);
                        }
                    }
                    
    
                    // Add debit to member's cash
                    if ($request->token2 == 2 && $this->isStockist($member->id)) {
                        $member->addDebit($deductCash, "Stockist Voucher", $desc, $rand);
                        
                        DB::commit(); // Commit the transaction
                        return true;
                        
                    } elseif ($request->token == 1) {
                        $member->addDebit($deductCash, "Voucher", $desc, $rand);
                        
                        // Personal Discount Bonus && Discount Referral Bonus
                        if ($pv > 0 && $settingsM[13]->content > 0) {
                            $pBonus = $settingsM[13]->content;
                            $personalBonus = ($pv * $pBonus) * 500;
                            $Pdesc = "Personal Discount Bonus";
                            $member->addEarnings($personalBonus, "Cash", $Pdesc, "", $rand);
                            
                            // Distribute points to the upline members (Bonus PV (Points))
                            $Pddesc = "Bonus PV Earnings from " . $member->username;
                            $this->distributePoints($member, "", $Pddesc, $pv, $store, $rand);
                            
                            //cashback to stockist
                            $this->stockistCashBack($request->pickup,$deductCash,$deductVoucher,$rand);
                          
                            //get upline
                            $uplines = Member::where('id', $member->referrer_id)->first();
                            if($uplines && !empty($uplines) && $settingsM[12]->content > 0){
                                //Give Discount Referral Bonus
                                $dBonus = $settingsM[12]->content;
                                $discountBonus = ($pv*$dBonus) * 500;
                                $Ddesc = "Discount Refereer Bonus From " . $member->username;
                                $uplines->addEarnings($discountBonus, "Cash", $Ddesc, $member->id,$rand);
    
                            }
                            
                        }
    
                        DB::commit(); // Commit the transaction
                        return true;
                    }
                    
                    
                    
                    DB::rollBack(); // Rollback the transaction
                    return false;
                }
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            // Log the exception or handle the error as necessary
            return false;
        }
    }


    protected function availableFundLastWorking($member,$store,$cartTotal,$request, $settingsM, $cartItems=null, $type = null,$rand){  
        
        if($store == 1){;
            $productPV = 0;
            
            if($type != null &&  $type == 1){
                $TransactionType = "Registration";
            }elseif($type != null && $type == 2){
                $TransactionType = "Package Update";
            }
            
            //get member's package  Entry Store Purchase
            $package = Transaction::where('member_id', $member->id)
                                ->where('type', $TransactionType)
                                ->OrderBy('id', "DESC")
                                ->first();

            foreach($cartItems as $items){
                $productPV = $productPV + ($items["pv"] * $items["quantity"]);
            }
                    
                    
            $sponsor = Member::where('id', $member->referrer_id)->first();
            
            if(isset($package) && !empty($package)){ 
                
                if($productPV <= $package->pv){
                    $pv = $productPV;
                }else{
                    $pv = $package->pv;
                }
                
                $amount = $package->amount;


                //check if totalcart is <= amount
                if($cartTotal <= $amount){ 

                    // Distribute points to the upline members (Bonus PV (Points))
                    $bonuspvs = $package->bonus_pv;
                    $bonusPv = ($pv * $bonuspvs);
                    $desc = "Bonus PV Earnings from ".$member->username;
                    //$this->distributePoints($member, $bonusPv, "Points");
                    $this->distributePoints($member, $bonusPv, $desc, $pv,$store,$rand);
                    
                    // Give direct referral bonus
                    $Ddesc = "Direct Referral Bonus From ". $member->username;
                    $this->distributeReferralBonus($sponsor, $pv , $Ddesc, $member->id,$rand,$settingsM);
                    
                    
                    if($type != null &&  $type == 1){
                        //call the method to distribute the indirect referral bonus
                        //$dbonus = $this->getDirectReferralBonusValue($member,$pv);
                        $desc = "Indirect Referral Bonus From " . $member->username;
                        $this->distributeDirectReferralBonus($member, $pv, $desc,$rand);
                        
                        //give team pairing bonus
                        $this->teamBonus($member,$rand);
                    }
                    

                
                    
                    //drop stockist registration bonus
                    $this->stockistRegistrationBonus($request->pickup,$rand);
                    
                    //cashback to stockist
                    $this->stockistCashBack($request->pickup,$cartTotal,"",$rand);
                    

                    return true;
                }else{ 
    
                    $cashBalance = $member->getBalance("Voucher"); 
                    $totalBalance = ($amount + $cashBalance); 
                    if($cashBalance > 0 && $totalBalance >= $cartTotal){ 
                        //deduct cash and return true
                        $cash = ($cartTotal - $amount); 
                        $desc = "Payment for Product"; //dd($cashBalance);

                        // Add debit to member's voucher
                        $member->addDebit($cash, "Voucher", $desc,$rand);

                        // Distribute points to the upline members (Bonus PV (Points))
                        $bonuspvs = $package->bonus_pv;
                        $bonusPv = ($pv * $bonuspvs);
                        $desc = "Bonus PV Earnings from ".$member->username;
                        //$this->distributePoints($member, $bonusPv, "Points");
                        $this->distributePoints($member, $bonusPv,$desc,$request->pv,$store,$rand);
                        
                        
                        //Give direct referral bonus
                        $Ddesc = "Direct Referral Bonus From ". $member->username;
                        $this->distributeReferralBonus($sponsor, $request->pv, $desc, $member->id,$rand,$settingsM);

                        if($type != null &&  $type == 1){
                            //give team pairing bonus
                            $this->teamBonus($member,$rand);
                        }
                        
                        //drop stockist registration bonus
                        $this->stockistRegistrationBonus($request->pickup,$rand);
                        
                        //cashback to stockist
                        $this->stockistCashBack($request->pickup,$cartTotal,"",$rand);
                    
                        return true;
                    }
                    return false;
                } 
            }
            
            
        }elseif($store == 2){ 

                $pv = 0;
                
                
                
                if($request->token2 == 2 && $this->isStockist($member->id)){ 
                    $cashBalance = $member->getBalance("Stockist Voucher");
                }else{
                    $cashBalance = $member->getBalance("Voucher");
                }
                
              
                
                if($cashBalance >= $cartTotal){ 
                    //deduct cash and return true
                    $desc = "Payment for Product"; //dd($cashBalance);

                    $cartItems = session()->get('cart', []);

                    foreach($cartItems as $items){
                        $pv = $pv + ($items["pv"]  * $items["quantity"]);
                    }
                    

                    
                    if($request->token2 == 2 && $this->isStockist($member->id)){
                        
                        $descL = "Restock Commission From ". $member->username;
                        //$stockistL = Member::select('members.*', 'pickups.*')
                                            //->join('pickups', 'pickups.member_id', '=', 'members.id')
                                            //->where('members.id', $member->id)
                                            //->where('pickups.status', 1)
                                            //->first();
                                            
                        $stockistL = Member::select('members.*', 'pickups.*', 'stockist_packages.*')
                                            ->join('pickups', 'pickups.member_id', '=', 'members.id')  
                                            ->join('stockist_packages', 'stockist_packages.id', '=', 'pickups.type')  
                                            ->where('members.id', $member->id)
                                            ->where('pickups.status', 1)
                                            ->first();
                                            
                                            
                                            
                        if($stockistL && !empty($stockistL) && $stockistL->restock_commission > 0)   {  
                            $stockistR = $stockistL->restock_commission;
                            $stockistCash = ($stockistR * $pv * 500);
                            $member->addEarnings($stockistCash, "Cash", $descL,$member->id,$rand);
                            
                            //if($stockistL->type == 1){
                                //member (sales outlet) earns 0.05 * point * 500
                                //$stockistCash = (0.05 * $pv * 500);
                                //$member->addEarnings($stockistCash, "Cash", $descL,$member->id,$rand);
                                
                            //}elseif($stockistL->type == 2){
                                //member (depot) earns 0.07 * point * 500
                                //$stockistCash = (0.07 * $pv * 500);
                                //$member->addEarnings($stockistCash, "Cash", $descL,$member->id,$rand);
                            //}
                        }
                        
                        if(!empty($member->referrer_id)){
                            
                            //member (depot) earns 0.02 * $stockistCash
                            $descR = "Stockist Sponsor Commission From". $member->username;
                            
                            $stockistReferral = Member::find($member->referrer_id);
                            
                            if(isset($stockistReferral) && $stockistL->sponsor_commission > 0){ 
                                $stockupline = $stockistL->sponsor_commission;
                                $stockistRCash = ($stockupline * $stockistCash);
                                $stockistReferral->addEarnings($stockistRCash, "Cash", $descR,$member->id,$rand);
                            }
                        }
                        
                        
                        //pickup location earns 0.03 * point * 500
                        $pickupL = Member::join('pickups', 'pickups.member_id', '=', 'members.id')
                                        ->where('pickups.id', $request->pickup)
                                        ->where('pickups.status', 1)
                                        ->first();
                        
                                        
                        //$pickupLM  = Member::find($pickupL->member_id); 
                        
                        $pickupLM  = Member::select('members.*', 'pickups.*', 'stockist_packages.*')
                                            ->join('pickups', 'pickups.member_id', '=', 'members.id')  
                                            ->join('stockist_packages', 'stockist_packages.id', '=', 'pickups.type')  
                                            ->where('members.id', $pickupL->member_id) 
                                            ->where('pickups.id', $pickupL->id)
                                            ->where('pickups.status', 1)
                                            ->first();
                                            
                        if(isset($pickupLM) && $pickupLM->pickup_restock_commission > 0){            
                            $pickupLMC = $pickupLM->pickup_restock_commission	;
                            $cashL =  ($pickupLMC * $pv * 500);
                            $pickupLM->addEarnings($cashL, "Cash", $descL,$member->id,$rand);
                        }
                        
                        
                        
                        // Add debit to member's voucher
                        $member->addDebit($cartTotal, "Stockist Voucher", $desc,$rand);
                    }else{
                        // Add debit to member's voucher
                        $member->addDebit($cartTotal, "Voucher", $desc,$rand);
                    }
                    
                    
                    
                    
                    if($request->token == 1){
                        // Distribute points to the upline members (Bonus PV (Points))
                        $Pdesc = "Bonus PV Earnings from ".$member->username;
                        $this->distributePoints($member, "",$Pdesc,$pv,$store,$rand);
    
                        //cashback to stockist
                        $this->stockistCashBack($request->pickup,$cartTotal,"",$rand);
                    }
                    
                    return true;
                }
                return false;
            
        
        }elseif($store == 3){
                
            
                $deductVoucher = $discount = $pv = $deductCash = 0;
                
                if($request->token2 == 2 && $this->isStockist($member->id)){
                    $cashBalance = $member->getBalance("Stockist Voucher");
                    $voucherBalance = $member->getBalance("Stockist Free Voucher");
                }else{
                    $cashBalance = $member->getBalance("Voucher");
                    $voucherBalance = $member->getBalance("Free Voucher");
                }
                
                $cartItems = session()->get('cart', []);

                foreach($cartItems as $items){
                    $discount = ($items["discount"] * $items["quantity"]) + $discount;
                    $pv = $pv + ($items["pv"] * $items["quantity"]);
                }
                
                $deductVoucher = $discount;
                $deductCash = $cartTotal - $deductVoucher;
                $totalBalance = $cashBalance + $voucherBalance;


                if($totalBalance >= $cartTotal && $voucherBalance >= $deductVoucher && $cashBalance >= $deductCash){ 
                    
                    //deduct cash and return true
                    $desc = "Payment for Product"; //dd($cashBalance);

                    
                    
                    
                    
                    if($discount > 0){
                        $deductVoucher = $discount;  //($discount/100) * $cartTotal;
                        
                        // Add debit to member's voucher
                        if($request->token2 == 2 && $this->isStockist($member->id)){
                            $member->addDebit($deductVoucher, "Stockist Free Voucher", $desc,$rand);
                        }else{
                            $member->addDebit($deductVoucher, "Free Voucher", $desc,$rand);
                        }
                    }

                    

                    // Add debit to member's cash
                    if($request->token2 == 2 && $this->isStockist($member->id)){
                        $member->addDebit($deductCash, "Stockist Voucher", $desc,$rand);
                    }else{
                        $member->addDebit($deductCash, "Voucher", $desc,$rand);
                    }

//dd($totalBalance);
                    

                    if($request->token == 1){
                        //Personal Discount Bonus && Discount Referral Bonus
                        if($pv > 0 && $settingsM[13]->content > 0){
                            $pBonus = $settingsM[13]->content;
                            $personalBonus = ($pv*$pBonus) * 500;
                            $Pdesc = "Personal Discount Bonus";
                            $member->addEarnings($personalBonus, "Cash", $Pdesc,"",$rand);
    
                            //get upline
                            $uplines = Member::where('id', $member->referrer_id)->first();
                            if($uplines && !empty($uplines) && $settingsM[12]->content > 0){
                                //Give Discount Referral Bonus
                                $dBonus = $settingsM[12]->content;
                                $discountBonus = ($pv*$dBonus) * 500;
                                $Ddesc = "Discount Refereer Bonus From " . $member->username;
                                $uplines->addEarnings($discountBonus, "Cash", $Ddesc, $member->id,$rand);
    
                            }
    
                            
                        }
                        
                        // Distribute points to the upline members (Bonus PV (Points))
                        $Pdesc = "Bonus PV Earnings from ".$member->username;
                        $this->distributePoints($member, "",$Pdesc,$pv, $store,$rand);
    
                        //cashback to stockist
                        $this->stockistCashBack($request->pickup,$deductCash,$deductVoucher,$rand);
                    }
                    
                    
                    return true;
                }
                

            return false;
        }
    }

    protected function stockistCashBack($pickup,$voucher,$freeVoucher =null,$rand){
        
        $pickup = Pickup::find($pickup);
        if(isset($pickup) && !empty($pickup)){
            $stockist = $this->memberdetails($pickup->member_id);
               
            
            $desc = "Stockist Cash Back";
            
            if(isset($voucher) && $voucher != null){
                $type = "Stockist Voucher";
                $stockist->addEarnings($voucher, $type, $desc, $stockist->id,$rand);
            }
            
            if(isset($freeVoucher) && $freeVoucher != null){
                $type = "Stockist Free Voucher";
                $stockist->addEarnings($freeVoucher, $type, $desc, $stockist->id,$rand);
            }
            
        }
        
    }
    
    protected function stockistRegistrationBonus($pickup,$rand){
        //settle stockist
        
        $pickup = Pickup::select('pickups.*', 'stockist_packages.*')
                        ->join('stockist_packages', 'pickups.type', '=', 'stockist_packages.id')
                        ->where('pickups.id', $pickup)
                        ->first();
        
        
        
        //Pickup::find($pickup);
        if(isset($pickup) && !empty($pickup)){
            $stockist = $this->memberdetails($pickup->member_id);
            //if($pickup->type == 1){
                //$value = "3000";
            //}elseif($pickup->type == 2){
                //$value = "5000";
            //}
            
            $value = $pickup->commission;
            
                
            $type = "Cash";
            $desc = "Stockist Registration Commission";
            if($value && isset($value) && !empty($value)){
                $stockist->addEarnings($value, $type, $desc, $stockist->id,$rand);
            }
            
        }
    }


    public function mytransactions(){
        $user = Auth::guard('web')->user();
        $title = "My Transactions";
        $mytransactions = Transaction::where('member_id', $user->id)->orderBy('id',"DESC")->get();
        
        return view('members.mytransactions', ['title' => $title, 'user' => $user, 'mytransactions' => $mytransactions]);
    }

    public function mypackages(){
        $user = Auth::guard('web')->user();
        $title = "My Packages";
        $mypackages = $this->getMemberPackages($user->id);
        $lastPackage = MembersPackage::where('member_id',$user->id)->orderby('id',"DESC")->first();

        
        $packages = Package::where('id', '>', $lastPackage->package_id)->get();
        //dd($lastPackage);
        return view('members.mypackages', ['title' => $title, 'user' => $user, 'mypackages' => $mypackages, 'packages' => $packages, 'prevPackage' => $lastPackage]);
    }

    public function upgradepackage(Request $request){

        $request->validate([
            'package'       => 'required|integer',
            'prePackage'    => 'required|integer',
        ]);

        $user = Auth::guard('web')->user();

        $package = Package::where('id',  $request->package)->first();
        $prePackage = Package::where('id',  $request->prePackage)->first();

        $upgradePrice = (($package->price) - ($prePackage->price));
        if($package  && !empty($package)){
            $pgradedPrice = $upgradePrice;
            $pgradedPv  = (($package->actual_pv) - ($prePackage->actual_pv));
            
            //check if cash balance is >= $pgradedPrice
            $cashBalance = $user->getBalance("Voucher"); 

            if($cashBalance >= $pgradedPrice){ 
                
                // Add debit to member's voucher
                $desc = "Package Upgrade";
                $user->addDebit($pgradedPrice, "Voucher", $desc);

                //add transaction details
                $transaction                    = new Transaction();
                $transaction->member_id         = $user->id;
                $transaction->type              = "Package Update";
                $transaction->amount            = $pgradedPrice;
                $transaction->pv                = $pgradedPv;
                $transaction->payment_method    = "Voucher";
                $transaction->status            = 1;
                $transaction->save();
                

                //add package to members_packages table
                $mPackage                   = new MembersPackage();
                $mPackage->member_id        = $user->id;
                $mPackage->package_id       = $request->package;
                $mPackage->amount           = $package->price;
                $mPackage->transaction_id   = $transaction->id;
                $mPackage->subcribe_date    = now();
                $mPackage->save();
                
                
                return back()->with('success', 'Upgrade was successful');
            }

                return back()->with('error', 'Insufficient balance');
        }
        
        return back()->with('error', 'Invalid package selection');
    }

    public function myorders(){
        $user = Auth::guard('web')->user();
        $title = "My Orders";
        $orders = Order::OrderBy('id', "DESC")->where('member_id', $user->id)->paginate(10);
        
        return view('members.myorders', ['title' => $title, 'user' => $user, 'orders' => $orders]);
    }
    
    
    public function stockistBackOffice(){
        $user = Auth::guard('web')->user();
        $title = "Pickup Orders";
        
        $totalOrdersCount = Order::join('pickups', 'orders.pickup_id', '=', 'pickups.id')
                                ->where('pickups.member_id', $user->id)
                                ->count();
        
        $orders = Order::select('orders.id as order_id', 'orders.member_id as memberid', 'orders.status as order_status', 'orders.*', 'pickups.*')
                        ->join('pickups', 'orders.pickup_id', '=', 'pickups.id')
                        ->where('pickups.member_id', $user->id)
                        ->OrderBy('orders.id', 'ASC')
                        ->OrderBy('orders.status', 'DESC')
                        ->paginate(10);
                        
                        
                        
        $packagetype = StockistPackage::select('stockist_packages.*', 'pickups.*')
                                    ->join('pickups', 'stockist_packages.id', '=', 'pickups.type')
                                    ->where('pickups.member_id', $user->id)
                                    ->OrderBy('pickups.id', "DESC")
                                    ->first();
                           
        $totalPendingOrdersCount = Order::join('pickups', 'orders.pickup_id', '=', 'pickups.id')
                                        ->where('pickups.member_id', $user->id)
                                        ->where('orders.status', '!=', 3)
                                        ->count();
        
        return view('members.stockists', ['title' => $title, 'user' => $user, 'orders' => $orders, 'totalOrdersCount' => $totalOrdersCount, 'totalPendingOrdersCount' => $totalPendingOrdersCount, 'packagetype' => $packagetype]);
        
    }
    
    public function stockistprocessorder($id){
        $user = Auth::guard('web')->user();
       
       
        $id = Crypt::decrypt($id); 
        $data = Order::where('id',$id)->first();
        
        if($data){
            $data->status = 2;
            $data->update();
            
            return back()->with('success', 'Order has been updated successfully.');
        }
    }
    
    public function stockistconfirmorder($id){
        $user = Auth::guard('web')->user();
        
        $id = Crypt::decrypt($id);
        $data = Order::where('id',$id)->first();
        
        if($data){
            $data->status = 3;
            $data->update();
            
            return back()->with('success', 'Order has been updated successfully.');
        }
    }
    
    public function getAllUplines($member)
    {
        $uplines = [];
    
        // Start from the current member and move upwards
        $currentMember = $member;
    
        while ($currentMember) {
            $upline = Member::where('left_leg_id', $currentMember->id)
                            ->orWhere('right_leg_id', $currentMember->id)
                            ->first();
    
            if ($upline) {
                $uplines[] = $upline; // Add upline to the list
                $currentMember = $upline; // Move up to the next upline
            } else {
                break; // No more uplines, exit the loop
            }
        }
    
        return $uplines; // Return the list of uplines
    }


    protected function distributePoints($member, $points = null, $desc, $pv = null, $store = null,$rand)
    {
        $currentMember = $member; 
        $type = 'Points';
        $pdesc = "Actual PV From " . $member->username;
        $pv = $pv;
        $points = $points;
        $uplines = $this->getAllUplines($member);
    
        // Drop package pv for new member
        $member->addEarnings($pv, "Points", "Personal Point", $member->id,$rand);
    
    
        if(isset($uplines) && count($uplines) > 0){
            foreach($uplines as $upline){
                
                // Drop package actual pv for upline
                $upline->addEarnings($pv, $type, $pdesc, $member->id,$rand);
                
                
                if(isset($store) && $store != null && $store == 1){
                    // Drop bonus pv for upline
                    $upline->addEarnings($points, $type, $desc, $member->id,$rand);
                }
            }
        }
        
    }


    public function distributeReferralBonus($member, $pv, $desc, $memberid,$rand,$settingsM)
    {
        
        if(isset($settingsM[16]) && $settingsM[16]->content > 0){
            // Calculate referral bonus
            $bon = $settingsM[16]->content;
            $rBonus = ($pv * $bon) * 500;
    
            // Check if the member has a referrer
            if (!empty($member)) {
                // Create the Earning record for the referrer
                $member->addEarnings($rBonus,"Cash",$desc, $memberid,$rand);
                
            }
        }
    }

    protected function getDirectReferralBonusValueold($member,$pv){
        $package = $this->getMemberPackage($member->id);

        $data = [];

        if (isset($package) && !empty($package)) {
            $data = [];

            switch ($package->id) {
                case 1:
                    $data= [
                        'level1' => 0.03 * $pv,
                        'level2' => 0,
                        'level3' => 0,
                        'level4' => 0,
                        'level5' => 0,
                        'level6' => 0,
                        'level7' => 0,
                        'level8' => 0,
                        'level9' => 0,
                    ];
                    break;

                case 2:
                    $data= [
                        'level1' => 0.03 * $pv,
                        'level2' => 0.02 * $pv,
                        'level3' => 0,
                        'level4' => 0,
                        'level5' => 0,
                        'level6' => 0,
                        'level7' => 0,
                        'level8' => 0,
                        'level9' => 0,
                    ];
                    break;

                case 3:
                    $data= [
                        'level1' => 0.03 * $pv,
                        'level2' => 0.02 * $pv,
                        'level3' => 0.01 * $pv,
                        'level4' => 0,
                        'level5' => 0,
                        'level6' => 0,
                        'level7' => 0,
                        'level8' => 0,
                        'level9' => 0,
                    ];
                    break;

                case 4:
                    $data = [
                        'level1' => 0.03 * $pv,
                        'level2' => 0.02 * $pv,
                        'level3' => 0.01 * $pv,
                        'level4' => 0.01 * $pv,
                        'level5' => 0.01 * $pv,
                        'level6' => 0.005 * $pv,
                        'level7' => 0.005 * $pv,
                        'level8' => 0.005 * $pv,
                        'level9' => 0.005 * $pv,
                    ];
                    break;

                // Add more cases if needed
            }
        }
  
  
        return $data;

    }
    
    protected function getDirectReferralBonusValue($member, $pv) {
        $package = $this->getMemberPackage($member->id); // Get package based on the member
    
        $data = [];
    
        if (isset($package) && !empty($package)) {
            // Fetch the bonus rates for this package from the database
            $bonusRates = DB::table('package_bonus_rates')
                ->where('package_id', $package->id)
                ->orderBy('level', 'asc')  // Ensure levels are ordered correctly
                ->get();
    
            // Check if the rates are available and structure them
            foreach ($bonusRates as $bonusRate) {
                $data['level' . $bonusRate->level] = $bonusRate->bonus_rate * $pv;
            }
        }
    
        return $data;
    }


    protected function distributeDirectReferralBonus($member, $pv, $desc,$rand)
    {
        $currentLevel = 1;
        $uplines = $this->getAllUplines($member); 
             
             //dd(count($uplines));
          
    
        if(isset($uplines) && count($uplines) > 0){
            foreach($uplines as $key => $upline){ //dd($uplines);
                /*if($currentLevel == 1){
                    if($member->referrer_id == $upline->id){
                        $sponsor = $upline;
                    }else{
                        $sponsor = Member::where('id',$member->referrer_id)->first();
                    }
                }else{
                    $sponsor = $upline;
                }*/
                
                $sponsor = $upline;
                
                $points = $this->getDirectReferralBonusValue($sponsor,$pv);
                
                // Check if the points array has the current level
                if (isset($points["level{$currentLevel}"]) && $points["level{$currentLevel}"] > 0) {
                    $point = $points["level{$currentLevel}"];
                    $pointValue = $point * 500;  // Multiply points by 500
        
                    // Ensure the sponsor is not null and points are greater than 0
                    if ($point > 0 && $sponsor) {
                        // Create the Earning record for the direct referral bonus
                        $sponsor->addEarnings($pointValue, "Cash", $desc, $member->id,$rand);
                        
                    }
                } 
                
                //if($currentLevel == 10){ break; }
                $currentLevel++;
            }
        }
    
    }
    
    public function withdrawals(Request $request)
    {
        $user = Auth::guard('web')->user();
        $title = "Withdrawals";
        $desc = "Withdrawal Request";
        
        // Validate the input data
        $request->validate([
            'from'  => 'nullable|date|before_or_equal:today', 
            'to'    => 'nullable|date|after_or_equal:from|before_or_equal:today', 
        ]);
    
        // Get the fromdate and todate from the request, if available
        $fromdate = $request->input('from');
        $todate = $request->input('to');
    
        // Initialize the query for fetching debits and applying filters
        $debitsQuery = Debit::where('type', 'Cash')
            ->where('member_id', $user->id)
            ->where('description', 'LIKE', '%' . e($desc) . '%');
    
        // Apply date filters if provided
        if ($fromdate && $todate) {
            $debitsQuery->whereBetween('created_at', [$fromdate, $todate]);
        }
    
        // Clone the query to calculate the sum of the value column
        $totalValue = (clone $debitsQuery)->sum('value');
    
        // Paginate the result
        $wallets = $debitsQuery->orderBy('created_at', 'desc')->paginate(10);
    
        return view('members.withdrawals', compact('user', 'title', 'wallets', 'totalValue'));
    }
    
    public function requestwithdrawal(){
        $user = Auth::guard('web')->user();
        $title = "Withdrawal Request Form";
        
        //check if member bank account is filled and check if they have the first two legs
        if(!empty($user->left_leg_id) && !empty($user->right_leg_id )  && !empty($user->bankaccount)  && !empty($user->bankname)){
           return view('members.requestwithdrawal', compact('user', 'title')); 
        }
        
        
        return back()->with('error', 'You are not qualified to make a withdrawal at the moment.');
        
    }
    
    public static function withdrawdetails($rand,$userid){
        $voucher = Earning::where('transaction_id',$rand)->where('member_id', $userid)->first();
        $charges = Debit::where('transaction_id',$rand)->where('member_id', $userid)->where('type', "Cash")->where('description','Withdrawal Charges')->first();
        $array = [];
        $Vcash = $Ccash = "";
        
        if(!empty($voucher)){
            $Vcash = $voucher->value;
        }
        
        if(!empty($charges)){
            $Ccash = $charges->value;
        }
        
        
        
        $array = [$Vcash,$Ccash];
        
        return $array;
    }
    
    
    public function processwithdrawal(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        $request->validate([
            'amount' => 'required|integer|min:5001',  // Ensure the amount is at least 5001 to avoid checking manually
        ]);
        
        $amount = $request->amount;
        $balance = $user->getBalance("Cash");
        
        
        // Start a database transaction
        DB::beginTransaction();
    
        try {
            
            if($balance >= $amount){
                $rand = $this->RandomString(8);
        
                $charges = 0.05 * $amount;
                
                $user->addDebit($charges, "Cash", "Withdrawal Charges", $rand);
        
                $Wcash = ($amount - ($charges));
                $desc = "Withdrawal Request";
        
                $user->addDebit($Wcash, "Cash", $desc, $rand);
        
                // Commit the transaction if everything is successful
                DB::commit();
        
                return redirect('withdrawals')->with('success', 'Your request was successful.');
            }
        } catch (\Exception $e) {
            // Rollback the transaction if there's any error
            DB::rollBack();
    
            // Log the error for debugging
            Log::error('Withdrawal process failed: ' . $e->getMessage());
    
            return back()->with('error', 'An error occurred during your request. Please try again.');
        }
    }




    public function wallets(Request $request)
    {
        $user = Auth::guard('web')->user();
        $title = "My Wallets";
    
        // Base query for debits
        $debits = Debit::select('id', 'member_id', 'type', 'value', 'description', 'created_at', DB::raw("'debit' as transaction_type"))
            ->where('member_id', $user->id);
    
        // Base query for earnings
        $earnings = Earning::select('id', 'member_id', 'type', 'value', 'description', 'created_at', DB::raw("'earning' as transaction_type"))
            ->where('member_id', $user->id);
    
        // Check if the form was submitted or any query parameter is present
        if ($request->isMethod('post') || $request->hasAny(['wallet', 'type', 'from', 'to', 'keywords'])) {
            // Validate the inputs
            $request->validate([
                'wallet'    => 'nullable|string|max:50',
                'type'      => 'nullable|string|in:Earning,Debit',
                'from'      => 'nullable|date',
                'to'        => 'nullable|date|after_or_equal:from',
                'keywords'  => 'nullable|string|max:255',
            ]);
    
            // Apply filters
            if ($request->wallet) {
                $debits->where('type', $request->wallet);
                $earnings->where('type', $request->wallet);
            }
    
            if ($request->type) {
                if ($request->type == 'Earning') {
                    $debits->whereRaw('1 = 0'); // Exclude debits
                } elseif ($request->type == 'Debit') {
                    $earnings->whereRaw('1 = 0'); // Exclude earnings
                }
            }
    
            if ($request->from) {
                $debits->whereDate('created_at', '>=', $request->from);
                $earnings->whereDate('created_at', '>=', $request->from);
            }
            if ($request->to) {
                $debits->whereDate('created_at', '<=', $request->to);
                $earnings->whereDate('created_at', '<=', $request->to);
            }
    
            if ($request->keywords) {
                $keywords = '%' . $request->keywords . '%';
                $debits->where('description', 'LIKE', $keywords);
                $earnings->where('description', 'LIKE', $keywords);
            }
        }
    
        // Combine both queries using union and sort them by created_at before paginating
        $wallets = $debits->union($earnings)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());
    
        return view('members.wallets', compact('user', 'title', 'wallets'));
    }

    public function transferVoucher(){
        $user = Auth::guard('web')->user();
        $title = "Transfer Voucher";
        $usernames = Member::OrderBy('username',"ASC")->get();


        return view('members.transfervoucher', compact('user', 'title', 'usernames'));
    }

    public function transfermembervoucher(Request $request){
        $request->validate([
            'member'      => 'required|integer',
            'value'       => 'required|integer',
        ]); 

        $user = Auth::guard('web')->user();
        $member = Member::find($request->member);
        $type = "Voucher";
        $voucherBalance = $user->getBalance("Voucher");
        
        

        if($voucherBalance >= $request->value){
            if(isset($member) && !empty($member)){
                
                //credit member with voucher
                $desc = "Transfered By". $user->username;
                $member->addEarnings($request->value, $type, $desc);
    
                //debit member with voucher
                $Ddesc = "Voucher was sent to". $member->username;
                $user->addDebit($request->value, $type, $Ddesc);
                
                return back()->with('success', 'Member has been credited successfully.');
            }
            
            return back()->with('error', 'Error in crediting member.');
        }

        return back()->with('error', 'Error in crediting member. Insufficient fund.');
    }

    public static function calculateUserRank($userId)
    {
        $member = Member::find($userId);
        
        
        if($member && !empty($member)){
            // Get the total points for the user
            $totalPoints = Earning::where('member_id', $userId)
                                ->where('type', 'Points')
                                ->sum('value');
                                
            $Leftpoints = $member->getTotalEarningsFromLeftLeg("Points");
            $Rightpoints = $member->getTotalEarningsFromRightLeg("Points") ;
    
    
    //dd($Leftpoints);
            // Determine the rank based on the total points
            /*if ($Leftpoints >= 35000000 &&  $Rightpoints >= 35000000) {
                return 'Elite';
            } elseif ($Leftpoints >= 17000000 &&  $Rightpoints >= 17000000) {
                return 'Royal Diamond';
            } elseif ($Leftpoints >= 8000000 &&  $Rightpoints >= 8000000) {
                return 'Crown Diamond';
            } elseif ($Leftpoints >= 3500000 &&  $Rightpoints >= 3500000) {
                return 'Sapphire';
            } elseif ($Leftpoints >= 1400000 &&  $Rightpoints >= 1400000) {
                return 'Emerald';
            } elseif ($Leftpoints >= 555000 &&  $Rightpoints >= 555000) {
                return 'Ruby';
            } elseif ($Leftpoints >= 185000 &&  $Rightpoints >= 185000) {
                return 'Diamond';
            } elseif ($Leftpoints >= 70000 &&  $Rightpoints >= 70000) {
                return 'Platinum';
            } elseif ($Leftpoints >= 28000 &&  $Rightpoints >= 28000) {
                return 'Gold';
            } elseif ($Leftpoints >= 5000 &&  $Rightpoints >= 5000) {
                return 'Silver';
            } elseif ($Leftpoints >= 1500 &&  $Rightpoints >= 1500) {
                return 'Bronze';
            } else {
                return 'No Rank';
            }*/
            
            $rank = DB::table('rank_thresholds')
                        ->where('left_points_threshold', '<=', $Leftpoints)
                        ->where('right_points_threshold', '<=', $Rightpoints)
                        ->orderBy('left_points_threshold', 'DESC')
                        ->orderBy('right_points_threshold', 'DESC')
                        ->first();
                    
            return $rank ? $rank->rank : 'No Rank';
        }
    }

    public function hasMetMonthlyPV($userId)
    { 
        // Get the current month and year
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $member = Member::find($userId);

        // Get the user's rank
        $rank = $this->calculateUserRank($userId);
        
        /*$totalPoints = Earning::where('member_id', $userId)
                                ->where('type', 'Points')
                                ->sum('value');
        */    
        
        $Leftpoints = $member->getTotalEarningsFromLeftLeg("Points");
        $Rightpoints = $member->getTotalEarningsFromRightLeg("Points") ;
        
        // Get the user's rank based on the left and right points
        $rankData = DB::table('rank_thresholds')
            ->where('left_points_threshold', '<=', $Leftpoints)
            ->where('right_points_threshold', '<=', $Rightpoints)
            ->orderBy('left_points_threshold', 'DESC')
            ->orderBy('right_points_threshold', 'DESC')
            ->first();
        
        // Default to 'No Rank' if no rank is found
        $rank = $rankData ? $rankData->rank : 'No Rank';
        $requiredPV = $rankData ? $rankData->pv_requirement : 20;
        
        // Calculate the total PV for the current month
        $monthlyPV = Earning::where('member_id', $userId)
                            ->where('type', 'Points')
                            ->where('description', 'Personal Point')
                            //->whereMonth('created_at', $currentMonth)
                            //->whereYear('created_at', $currentYear)
                            ->sum('value');
        
        // Check if the user has met the required PV for their rank
        return $monthlyPV >= $requiredPV;

    }

    // Helper method to get the bonus multiplier based on rank
    protected function getBonusMultiplier($rank)
    {
        $rankMultipliers = [
            'Bronze' => 0.25,
            'Silver' => 0.3,
            'Gold' => 0.3,
            'Platinum' => 0.35,
            'Diamond' => 0.35,
            'Ruby' => 0.35,
            'Emerald' => 0.35,
            'Sapphire' => 0.4,
            'Crown Diamond' => 0.4,
            'Royal Diamond' => 0.4,
            'Elite' => 0.5,
        ];

        return $rankMultipliers[$rank] ?? 0;
    }

    public function hasMetMonthlyPVold($userId)
    { 
        // Get the current month and year
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Get the user's rank
        $rank = $this->calculateUserRank($userId);
        

        // Define the minimum PV required for each rank
        $rankPVRequirements = [
            'No Rank'       => 20,
            'Bronze'        => 20,
            'Silver'        => 40,
            'Gold'          => 40,
            'Platinum'      => 70,
            'Diamond'       => 70,
            'Ruby'          => 100,
            'Emerald'       => 100,
            'Sapphire'      => 100,
            'Crown Diamond' => 200,
            'Royal Diamond' => 800,
            'Elite'         => 1400,
        ];

        // Get the minimum PV required for the user's rank
        $requiredPV = $rankPVRequirements[$rank] ?? 0;

        // Calculate the total PV for the current month
        $monthlyPV = Earning::where('member_id', $userId)
                            ->where('type', 'Points')
                            ->where('description', 'Personal Point')
                            //->whereMonth('created_at', $currentMonth)
                            //->whereYear('created_at', $currentYear)
                            ->sum('value');
//dd($monthlyPV);
        // Check if the user has met the required PV for their rank
        return $monthlyPV >= $requiredPV;
        

    }


    // Method to perform the monthly calculation
    public function calculateMonthlyTeamPerformanceBonusOLD()
    {
        // Get all members
        $members = Member::all();

        // Loop through each member
        foreach ($members as $member) {
            // Get all members referred by this member
            $referredMembers = $member->referredMembers;

            foreach ($referredMembers as $rCount => $referredMember) {
                // Calculate the total Indirect Referral Bonus for the referred member
                $totalIndirectReferralBonus = Earning::where('member_id', $referredMember->id)
                    ->where('type', 'Cash')
                    ->where('description', 'LIKE', 'Indirect Referral Bonus From ')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('value');

                //calculate 10% monthly personal bonus on repurchases
                $currentMonth = now()->month;
                $currentYear = now()->year;

                // Check total purchases from Order table for the current month where store = 2
                $totalPurchases = $this->calculateRepurchasePV($member->d, 'monthly', $currentMonth, $currentYear);

                $this->settleMonthlyRepurchaseReferralBonus($member,$totalPurchases);

                $this->settleMonthlyRepurchaseBonus($member,$totalPurchases);

                // Check if the referred member has met the minimum monthly PV
                if ($this->hasMetMonthlyPV($member->id)) {
                    
                    $bonusPV = (0.10 * $totalPurchases) * 500;

                    if($bonusPV > 0){
                        // Add the bonus to the member's earnings
                        $member->addEarnings($bonusPV, 'Cash', '10% Monthly Re-Purchase Bonus for ' . now()->format('F Y'), $member->id);
                    }         

                    // Calculate the bonus based on the rank of the referred member
                    $rank = $this->calculateUserRank($member->id);
                    $bonusMultiplier = $this->getBonusMultiplier($rank);
                    $bonus = $totalIndirectReferralBonus * $bonusMultiplier;

                    // Format the current month and year
                    $monthYear = now()->format('F Y');

                

                    if($bonus > 0){
                        // Add earnings to the member who referred them with month included in the description
                        $member->addEarnings($bonus, 'Cash', "Monthly Team Performance Bonus for {$monthYear}", $member->id);
                    }
                }
            }
        }
    }
    
    
    public function calculateMonthlyTeamPerformanceBonus()
    {
        // Get all members
        $members = Member::all();

        // Loop through each member
        foreach ($members as $member) {
            // Get all members referred by this member
            $referredMembers = $member->referredMembers;

            foreach ($referredMembers as $rCount => $referredMember) {
                // Calculate the total Indirect Referral Bonus for the referred member
                $totalIndirectReferralBonus = Earning::where('member_id', $referredMember->id)
                    ->where('type', 'Cash')
                    ->where('description', 'LIKE', 'Indirect Referral Bonus From ')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('value');

                //calculate 10% monthly personal bonus on repurchases
                $currentMonth = now()->month;
                $currentYear = now()->year;

                // Check total purchases from Order table for the current month where store = 2
                $totalPurchases = $this->calculateRepurchasePV($member->d, 'monthly', $currentMonth, $currentYear);

                $this->settleMonthlyRepurchaseBonus($member,$totalPurchases);

               
            }
        }
    }


    protected function settleMonthlyRepurchaseReferralBonus($member,$totalPurchases){
        $currentLevel = 1;
        $upline = $member;
        $monthYear = now()->format('F Y');

        while ($currentLevel <= 2 && $upline) { 
            $sponsor = Member::find($upline->referrer_id);
            if($currentLevel == 1){
                $bonus = 0.10 * $totalPurchases;
            }else{
                $bonus = 0.05 * $totalPurchases;
            }
            // Check if the referred member has met the minimum monthly PV
            if ($this->hasMetMonthlyPV($sponsor->id)) {
                $sponsor->addEarnings($bonus, 'Cash', "Monthly Repurchase Referral Bonus for {$monthYear}", $member->id);
            }

            $upline = Member::find($sponsor->referrer_id);
        }
    }

    public function settleMonthlyTeamBonus()
    { 
        // Get all members at once
        $members = Member::all(); 
    
        if ($members->isEmpty()) {
            return;
        }
        
        $month = $month ?? now()->month; 
        $year = $year ?? now()->year;   
        $from = date('Y-m-01');
        $to = date('Y-m-t');

    
        // Loop through each member
        foreach ($members as $member) {
            // Check if the member qualifies for the monthly PV
            if ($this->hasMetMonthlyPV($member->id)) {
                
                // Fetch left and right points for the member
                $leftPoints = $member->getTotalEarningsFromLeftLeg("Points");
                $rightPoints = $member->getTotalEarningsFromRightLeg("Points");
                
    
                // Fetch the rank data for the current points
                $rank = $this->getRankBasedOnPoints($leftPoints, $rightPoints);
                
    
                if ($rank) {
                    // Fetch rank levels and bonus parameters
                    $rankLevel = RankLevel::all()->keyBy('ranklevel');
                    
                    //$pv = $this->calculateRepurchasePV($member->id, '', $month, $year);
                    
                    $pvdesc = "5% instant cash back";
                    //$startOfMonth = Carbon::now()->startOfMonth(); 
                    //$endOfRange = Carbon::now()->startOfMonth()->addDays(26); 
                    $startOfMonth = Carbon::now()->subMonth()->startOfMonth(); // 1st of the previous month
                    $endOfRange = Carbon::now()->subMonth()->startOfMonth()->addDays(26);

                    $pv = DB::table('earnings')
                                    ->whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($pvdesc) . '%'])
                                    ->where('member_id', $member->id)
                                    ->where('type', 'Cash')
                                    ->whereBetween('created_at', [$startOfMonth, $endOfRange])
                                    ->sum('value');
                    
                    
                    
                    if($pv > 0){
                        // Process team bonus logic using rank level (you can expand on this)
                        $this->processTeamBonus($member, $rank, $rankLevel,$pv);
                    }
                }
            }
        }
    }
    
    
    // Refactor rank fetching into its own method for reuse
    protected function getRankBasedOnPoints($leftPoints, $rightPoints)
    {
        return DB::table('rank_thresholds')
                ->where('left_points_threshold', '<=', $leftPoints)
                ->where('right_points_threshold', '<=', $rightPoints)
                ->orderBy('left_points_threshold', 'DESC')
                ->orderBy('right_points_threshold', 'DESC')
                ->first();
    }
    
    protected function processTeamBonus($member, $rank, $rankLevel, $pv)
    {
        $from = $rank->level_from;
        $to = $rank->level_to;
        
        
        for ($i = $from; $i <= $to; $i++) {
            
            if(isset($rankLevel[$i]) && $rankLevel[$i]->pv > 0){
                $bonusPv = $rankLevel[$i]->pv;
                $bonus = $bonusPv * $pv;
                
                
                $level = $rankLevel[$i]->ranklevel;
                
                $desc = "Monthly Team Bonus Earnings For Level".' '. $level;
                                
                $checkEarnings = Earning::whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($desc) . '%'])
                                        ->where('member_id', $member->id)
                                        ->where('type', "Cash")
                                        ->first();
    
                if (empty($checkEarnings)) {
                    // Add earnings
                    $rand = $this->RandomString("5");
                    $member->addEarnings($bonus, "Cash", $desc, $member->id, $rand);
                }
                
            }
            
            
        }
        
    }
    public function settleMonthlyRepurchaseBonus($member, $totalPurchases)
    {
        $monthYear = now()->format('F Y');
        // Define the rank levels and their corresponding max payable levels
        $rankLevels = [
            'Bronze' => 40,
            'Silver' => 40,
            'Gold' => 40,
            'Platinum' => 40,
            'Diamond' => 50,
            'Ruby' => 50,
            'Emerald' => 60,
            'Sapphire' => 60,
            'Crown Diamond' => 60,
            'Royal Diamond' => 70,
            'Elite' => 80,
        ];

        $memberRank = $this->calculateUserRank($member->id);

        if(!empty($memberRank)){
            // Get the maximum level payable for the member's rank
            $maxLevelPayable = $rankLevels[$memberRank] ?? '';

        
            // Define the commission rates based on the level ranges
            $commissionRates = [
                [1, 5, 0.02],
                [6, 15, 0.01],
                [16, 30, 0.005],
                [31, 80, 0.002],
            ];

            // Get the upline members
            $uplines = $this->getUplines($member->id, $maxLevelPayable);

            // Loop through each upline and calculate the earnings
            foreach ($uplines as $level => $upline) {
                // Ensure we are within the max level payable
                if ($level > $maxLevelPayable) {
                    break;
                }

                // Check if the upline meets the Minimum Monthly Purchase Volumes (PV)
                if ($this->hasMetMonthlyPV($upline)) {
                    // Determine the commission rate based on the upline's level
                    foreach ($commissionRates as $rate) {
                        if ($level >= $rate[0] && $level <= $rate[1]) {
                            $earnings = $totalPurchases * $rate[2];
                            $upline->addEarnings($earnings, 'Cash', "Monthly Repurchase Bonus for {$monthYear}", $member->id);
                            break;
                        }
                    }
                }
            }
        }
    }

    protected function getUplines($memberId, $maxLevelPayable)
    {
        $uplines = [];
        $currentMember = Member::find($memberId);
        $level = 1;
    
        while ($currentMember->referrer_id && $level <= $maxLevelPayable) {
            // Get the upline member
            $upline = Member::find($currentMember->referrer_id);
    
            // Add the upline to the array with the current level
            $uplines[$level] = $upline;
    
            // Move to the next upline
            $currentMember = $upline;
            $level++;
        }
    
        return $uplines;
    }
    

    public function calculateRepurchasePV($memberId, $period = 'all', $month = null, $year = null)
    {
        // Initialize the total PV
        $totalPV = 0;

        // Query to get the orders based on the period
        $ordersQuery = Order::where('member_id', $memberId)
                            ->where('store', 2);

        // Filter by month and year if specified
        if ($period === 'monthly' && $month && $year) {
            $ordersQuery->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year);
        } elseif ($period === 'yearly' && $year) {
            $ordersQuery->whereYear('created_at', $year);
        }

        // Fetch the orders
        $orders = $ordersQuery->get();

        // Loop through each order
        foreach ($orders as $order) {
            // Decode the items JSON
            $items = json_decode($order->items, true);

            // Loop through each item
            foreach ($items as $item) {
                // Get the product's PV from the Product table using the item ID
                $prod = Product::where('id', $item['id'])->first();
                if($prod && !empty($prod)){
                    $totalPV += $item['quantity'] * $prod->pv;
                }
                
                
            }
        }

        // Return the total PV
        return $totalPV;
    }



    public static function getPrice($price,$country)
    {
        $countryId = $country;
        return $price;
        //$currency = Currency::whereJsonContains('country', $countryId)->first();
        
        //if ($currency) { 
            //$amount =  ($price * $currency->conversion);
            //$symbol = html_entity_decode($currency->symbol);
            //$conversion = $symbol .' '. number_format($amount);

            //$data = @array('conversion' => $conversion, 'price' => $amount, 'symbol' => $symbol);
            //return $data;
        //} 
    }

    public static function getPickupLocation($id){
        $data = Pickup::where('id', $id)->first();
        return $data;
    }
    
    public static function packagedetails($id){
        $data = Package::where('id', $id)->first();
        return $data;
    }

    public static function validPick($userid,$pickupid,$pickuptype = null){

        //check if user is a stockist
        $isStockist = Pickup::where('member_id', $userid)->where('status',1)->first();
        
       

        if($isStockist && !empty($isStockist)){
            //check is stockist package position is lower than pickup location
            $package = $isStockist->type; //StockistPackage::where('id', $isStockist->type)->first();
            
            
            if($isStockist->id == $pickupid){
                return false;
            }
            
            if($package == 2 && ($pickupid != 4 && $pickupid != 7)){
                return false;
            }
            
            if(isset($pickuptype) && ($package == 1 && $pickuptype == $package)){
                return false;
            }
            
            /*if(isset($package) && $package == 2 && $pickupid != 1){
                return false;
            }*/
            
            return true;
        }

        return true;
    }

    public static function getStockistPackage($id){
        $data = StockistPackage::where('id', $id)->first();
        return $data;
    }
    
    public static function isStockist($userid){
        $data = Pickup::where('member_id', $userid)->where('status',1)->first();
        
        //dd($data);
        if($data && !empty($data)){
            return true;
        }
        return false;
    }

    public static function memberdetails($id){
        $data = Member::where('id', $id)->first();
        return $data;
    }

    public static function getMemberPackage($id){ 
        $data = MembersPackage::select('members_packages.*', 'packages.*')
                            ->join('packages', 'members_packages.package_id', '=', 'packages.id')
                            ->where('members_packages.member_id',$id)
                            ->OrderBy('members_packages.id', "DESC")
                            ->first();
        return $data;
    }

    public static function getMemberPackages($id){
        $data = MembersPackage::select('members_packages.*', 'packages.*')
                            ->join('packages', 'members_packages.package_id', '=', 'packages.id')
                            ->where('members_packages.member_id',$id)
                            ->OrderBy('members_packages.id', "ASC")
                            ->get();

        return $data;
    }

    public static function storedetails($id){
        $data = Store::where('id', $id)->first(); 
        return $data; 
    }
    
    
    public function processCronTeamBonus()
    {
        Artisan::call('process:team-bonuses');
    }

public function listFailedJobs()
{
    // Run the queue:failed command to get the list of failed jobs
    $output = Artisan::call('queue:failed');

    // Optionally, you can fetch the failed jobs from the database
    $failedJobs = \DB::table('failed_jobs')->get();

    return response()->json([
        'failed_jobs' => $failedJobs
    ]);
}

    
    public function RandomString($length, $charset='123456789'){
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count-1)];
        }
        return $str;
    }


    public function logout()
    {
        Auth::guard('web')->logout();

        //$request->session()->invalidate();
        //$request->session()->regenerateToken();

        return redirect('/login');
    }
}
