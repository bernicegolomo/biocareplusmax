<?php

namespace App\Http\Controllers;


use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Carbon\Carbon;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Pages;
use App\Models\Store;
use App\Models\Setting;
use App\Models\Page;
use App\Models\Package;
use App\Models\AccessToken;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Member;
use App\Models\MembersPackage;
use App\Models\Pickup;
use App\Models\Earning;
use App\Models\Debit;
use App\Models\Order;
use App\Models\StockistPackage;
use App\Models\Transaction;
use App\Models\Bank;
use App\Models\RankThreshold;
use App\Models\RankLevel;

class AdminController extends Controller
{
    //
    public function login(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Attempt to authenticate admin user
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            // If authentication succeeds, get the authenticated user
            $user = Auth::user();
            // Pass user data to the intended URL
            return redirect()->intended('/admin/dashboard')->with('user', $user);
        }

        // If authentication fails, redirect back with error message
        return redirect()->back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }


    public function dashboard()
    {  
        $user = Auth::guard('admin')->user();

        if ($user) {
            $title = "Dashboard"; 
            $members = Member::all();
            $activeMembers = $inactiveMembers = 0;
            
            $oneMonthAgo = now()->subDays(30);

            // Retrieve orders made in the last 30 days
            $currentMonthOrders = Order::where('created_at', '>=', $oneMonthAgo)
                                        ->get()
                                        ->groupBy('member_id');
            
            foreach ($members as $member) {
                if (isset($currentMonthOrders[$member->id])) {
                    $activeMembers += 1; 
                } else {
                    $inactiveMembers += 1;
                }
            }
            
            $orders = Order::count();
            
            return view('admin.dashboard', compact('title', 'user', 'activeMembers', 'inactiveMembers', 'orders'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }
    
    public function inactivemembers(){
        $user = Auth::guard('admin')->user();

        if ($user) {
            
            $title = "In Active Members";
            $oneMonthAgo = now()->subDays(30);
            $members = Member::select('members.*')
                            ->join('orders', 'orders.member_id', '=', 'members.id')
                            ->where('orders.created_at', '<', $oneMonthAgo)
                            ->paginate(10);
            
            
            return view('admin.othermembers', compact('title', 'user', 'members'));
            
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }
    
    
    public function activemembers(){
        $user = Auth::guard('admin')->user();

        if ($user) {
            
            $title = "Active Members";
            $oneMonthAgo = now()->subDays(30);
            $members = Member::select('members.*')
                            ->join('orders', 'orders.member_id', '=', 'members.id')
                            ->where('orders.created_at', '>=', $oneMonthAgo)
                            ->paginate(10);
            
            
            return view('admin.othermembers', compact('title', 'user', 'members'));
            
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }
    
    
    public function products(Request $request)
    {  
        $user = Auth::guard('admin')->user();

        if ($user) {
            $products = Product::OrderBy('id', "DESC")->get();
            $title = "Manage Products";
            return view('admin.products', compact('title', 'user', 'products'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }
    

    public function newproduct($id=null)
    {  
        $user = Auth::guard('admin')->user();


        if ($user) {
            $categories = Category::OrderBy('id', "DESC")->get();
            $stores = Store::Where('status', 1)->get();

            if($id != NULL){
                $title = "Update Product";
                $id = Crypt::decrypt($id);
                $data = Product::Find($id);
                return view('admin.newproduct', compact('title', 'user', 'categories', 'stores', 'data'));
            }else{
                $title = "Add Product"; 
                return view('admin.newproduct', compact('title', 'user', 'categories', 'stores'));
            }
            
            
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function createproduct(Request $request){

        $request->validate([
            'name'      => 'required|string|max:255',
            'image'     => 'required|image|mimes:jpg,jpeg,png|max:2048', // restrict to jpg, jpeg, png with max size 2048KB
            'qty'       => 'nullable|integer',
            'price'     => 'required|string',
            'pv'        => 'required|string',
            'discount'  => 'nullable|string',
            'oldprice'  => 'nullable|string',
            'category'  => 'required|string|max:105',
            'stores'    => 'required|array', // ensure stores is an array
            'stores.*'  => 'required|integer', // ensure each store is an integer
            'content'   => 'required|string',
        ]);

        $store = $request->input('stores');

        // Ensure all stores IDs are strings (if needed)
        $store = array_map('strval', $store);

        

        $product = new Product();
        $product->name          = $request->name;
        $product->category_id   = $request->category;
        $product->store         = json_encode($store);
        $product->qty           = $request->qty;
        $product->price         = $request->price;
        $product->pv            = $request->pv;
        $product->discount      = $request->discount;
        $product->oldprice      = $request->oldprice;
        $product->content       = sprintf($request->content);


        //check if feature image was uploaded and save it
        //save image
        if($request->hasFile('image')){
            $rand = $this->RandomString(8);
            $filename = $rand.'.'.$request->image->extension();
            $request->image->move('front/assets/images/products', $filename);
            $product->image = $filename;
        }     

        $product->save();

        return redirect('admin/products')->with('success', 'Product has been created successfully.');
    }

    public function deleteproduct($id){
        $id = Crypt::decrypt($id);
        $data = Product::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Product has been deleted successfully.');
        }
    }


    public function updateproduct(Request $request){
        $request->validate([
            'id'        => 'required|integer',
            'name'      => 'required|string|max:255',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // restrict to jpg, jpeg, png with max size 2048KB
            'qty'       => 'nullable|integer',
            'price'     => 'required|string',
            'pv'        => 'required|string',
            'discount'  => 'nullable|string',
            'oldprice'  => 'nullable|string',
            'category'  => 'required|string|max:105',
            'stores'    => 'required|array', // ensure stores is an array
            'stores.*'  => 'required|integer', // ensure each store is an integer
            'content'   => 'required|string',
        ]);

        $store = $request->input('stores');

        // Ensure all stores IDs are strings (if needed)
        $store = array_map('strval', $store);

        

        $product = Product::where('id', $request->id)->first();

        if($product && !empty($product )){
            $product->name          = $request->name;
            $product->category_id   = $request->category;
            $product->store         = json_encode($store);
            $product->qty           = $request->qty;
            $product->price         = $request->price;
            $product->pv            = $request->pv;
            $product->discount      = $request->discount;
            $product->oldprice      = $request->oldprice;
            $product->content       = sprintf($request->content);


            //check if feature image was uploaded and save it
            //save image
            if($request->hasFile('image')){
                $rand = $this->RandomString(8);
                $filename = $rand.'.'.$request->image->extension();
                $request->image->move('front/assets/images/products', $filename);
                $product->image = $filename;
            }     

            $product->update();
            return back()->with('success', 'Product has been updated successfully.');
        }

        return back()->with('error', 'Error updating product.');
    }
    
    public function categories(Request $request)
    {  
        $user = Auth::guard('admin')->user();

        if ($user) {
            $categories = Category::OrderBy('id', "DESC")->get();
            $title = "Manage Categories";
            return view('admin.categories', compact('title', 'user', 'categories'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }
    
    
    public function newcategory($id=null)
    {  
        $user = Auth::guard('admin')->user();


        if ($user) {
            
            if($id != NULL){
                $title = "Update Category";
                $id = Crypt::decrypt($id);
                $data = Category::Find($id);
                return view('admin.newcategory', compact('title', 'user', 'data'));
            }else{
                $title = "Add Category"; 
                return view('admin.newcategory', compact('title', 'user'));
            }
            
            
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function createcategory(Request $request){

        $request->validate([
            'name'      => 'required|string|max:255',
        ]);

       

        $category = new Category();
        $category->name          = $request->name;
        $category->save();

        return redirect('admin/categories')->with('success', 'Product category has been created successfully.');
    }

    public function deletecategory($id){
        $id = Crypt::decrypt($id);
        $data = Category::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Product category has been deleted successfully.');
        }
    }


    public function updatecategory(Request $request){
        $request->validate([
            'id'        => 'required|integer',
            'name'      => 'required|string|max:255',
        ]);

        $category = Category::where('id', $request->id)->first();

        if($category && !empty($category)){
            $category->name          = $request->name;
            $category->update();
            return redirect('admin/categories')->with('success', 'Product category has been updated successfully.');
        }

        return back()->with('error', 'Error updating product.');
    }
    
    

    public function stores(Request $request)
    {  
        $user = Auth::guard('admin')->user();

        if ($user) {
            $stores = Store::all();
            $title = "Manage Stores";
            return view('admin.stores', compact('title', 'user', 'stores'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function newstore($id=null)
    {  
        $user = Auth::guard('admin')->user();


        if ($user) {

            if($id != NULL){
                $title = "Update Store";
                $id = Crypt::decrypt($id);
                $data = Store::Find($id);
                return view('admin.newstore', compact('title', 'user', 'data'));
            }else{
                $title = "Add Store"; 
                return view('admin.newstore', compact('title', 'user'));
            }
            
            
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function createstore(Request $request){

        $request->validate([
            'name'      => 'required|string|max:255',
        ]);

        $store = new Store();
        $store->name    = $request->name;
        $store->status  = 1;
        $store->save();

        return redirect('admin/stores')->with('success', 'Store has been created successfully.');
    }

    public function deletestore($id){
        $id = Crypt::decrypt($id);
        $data = Store::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Store has been deleted successfully.');
        }
    }


    public function updatestore(Request $request){
        $request->validate([
            'id'        => 'required|integer',
            'name'      => 'required|string|max:255',
        ]);

        $store = Store::where('id', $request->id)->first();

        if($store && !empty($store)){
            $store->name = $request->name;
            $store->update();
            return redirect('admin/stores')->with('success', 'Store has been updated successfully.');
        }

        return back()->with('error', 'Error updating store.');
    }

    public function deactivatestore($id){
        $user = Auth::guard('admin')->user();


        if ($user) {
            if($id != NULL){
                $id = Crypt::decrypt($id);
                $store = Store::where('id', $id)->first();

                if($store && !empty($store)){
                    $store->status  = 0;
                    $store->update();
                    return back()->with('success', 'Store has been updated successfully.');
                }
            }
        }

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function activatestore($id){
        $user = Auth::guard('admin')->user();


        if ($user) {
            if($id != NULL){
                $id = Crypt::decrypt($id);
                $store = Store::where('id', $id)->first();

                if($store && !empty($store)){
                    $store->status  = 1;
                    $store->update();
                    return back()->with('success', 'Store has been updated successfully.');
                }
            }
        }

        return redirect('admin')->with('error', 'Unauthorized access.');
    }



    public function pickup(Request $request)
    {  
        $user = Auth::guard('admin')->user();

        if ($user) {
            $pickups = Pickup::all();
            $title = "Manage Pickup Location";
            return view('admin.pickup', compact('title', 'user', 'pickups'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function newpickup()
    {  
        $user = Auth::guard('admin')->user();
        $packages = StockistPackage::all();

        if ($user) {
            $title = "Add Pickup Location"; 
            $members = Member::select('members_packages.*', 'members.*')
                                    ->join('members_packages', 'members_packages.member_id', '=', 'members.id')
                                    ->where('members_packages.package_id', '>=', 4)
                                    ->OrderBy('members.name', "ASC")
                                    ->OrderBy('members.id', "DESC")
                                    ->get();

            return view('admin.newpickup', compact('title', 'user', 'members', 'packages'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function earnings(Request $request)
    { 
        $user = Auth::guard('admin')->user();
        $title = "Members Earnings";
        $totalLeftPoint = $totalRightPoint = $from = $to = 0;
        $member = null;
    
        // Prepare base queries
        $debits = Debit::select('id', 'member_id', 'type', 'value', 'description', 'created_at', DB::raw("'debit' as transaction_type"));
        $earnings = Earning::select('id', 'member_id', 'type', 'value', 'description', 'created_at', DB::raw("'earning' as transaction_type"));
    
        // Apply filters to both the queries
        $applyFilters = function($query) use ($request) {
            if ($request->wallet) {
                $query->where('type', $request->wallet);
            }
    
            if ($request->from) {
                $query->whereDate('created_at', '>=', $request->from);
            }
    
            if ($request->to) {
                $query->whereDate('created_at', '<=', $request->to);
            }
    
            if ($request->username) {
                $member = Member::where('username', $request->username)->first();
                if ($member) {
                    $query->where('member_id', $member->id);
                } else {
                    $query->whereRaw('1 = 0'); // No results if member doesn't exist
                }
            }
    
            if ($request->keywords) {
                $keywords = '%' . $request->keywords . '%';
                $query->where('description', 'LIKE', $keywords);
            }
        };
        
    
        // Apply filters to main query
        if ($request->isMethod('post') || $request->hasAny(['wallet', 'type', 'from', 'to', 'username', 'keywords', 'export', 'search'])) {
            $request->validate([
                'wallet'    => 'nullable|string|max:50',
                'type'      => 'nullable|string|in:Earning,Debit',
                'from'      => 'nullable|date',
                'to'        => 'nullable|date|after_or_equal:from',
                'username'  => 'nullable|string|max:255',
                'keywords'  => 'nullable|string|max:255',
            ]);
    
            // Apply filters to queries
            $applyFilters($debits);
            $applyFilters($earnings);
    
            if ($request->type) {
                if ($request->type == 'Earning') {
                    $debits->whereRaw('1 = 0'); // Exclude debits
                } elseif ($request->type == 'Debit') {
                    $earnings->whereRaw('1 = 0'); // Exclude earnings
                }
            }
        }
        
        // Calculate total left and right points if a member is found
        if($request->from){
            $from = $request->from;
            $to = $request->to;
        }
        if ($member) {
            $totalLeftPoint = $member->getTotalEarningsFromLeftLeg("Points", $from, $to); 
            $totalRightPoint = $member->getTotalEarningsFromRightLeg("Points", $from, $to); 
        } else {
            // Loop through all members if no specific member was found
            $members = Member::all();
            foreach ($members as $memberz) {
                $totalLeftPoint += $memberz->getTotalEarningsFromLeftLeg("Points", $from, $to);
                $totalRightPoint += $memberz->getTotalEarningsFromRightLeg("Points", $from, $to);
            }
        }
        
       
        
        // Check if the export is requested
        if (isset($request->export) && $request->export == 1) { 
            $this->exportToCSV($debits, $earnings, $applyFilters); 
            return; // Exit after exporting
        }
        
        // Combine, order, and paginate
        $wallets = DB::table(DB::raw("({$debits->union($earnings)->toSql()}) as combined"))
                ->mergeBindings($debits->getQuery())
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends($request->all());
        
    
        // Calculate filtered total amounts
        $totalEarningCash = Earning::where('type', 'Cash')->where(function ($query) use ($applyFilters) {
            $applyFilters($query);
        })->sum('value');
    
        $totalDebitCash = Debit::where('type', 'Cash')->where(function ($query) use ($applyFilters) {
            $applyFilters($query);
        })->sum('value');
    
        $totalEarningPoints = Earning::where('type', 'Points')->where(function ($query) use ($applyFilters) {
            $applyFilters($query);
        })->sum('value');
    
        $totalDebitPoints = Debit::where('type', 'Points')->where(function ($query) use ($applyFilters) {
            $applyFilters($query);
        })->sum('value');
    
        $totalEarningVoucher = Earning::where('type', 'Voucher')->where(function ($query) use ($applyFilters) {
            $applyFilters($query);
        })->sum('value');
    
        $totalDebitVoucher = Debit::where('type', 'Voucher')->where(function ($query) use ($applyFilters) {
            $applyFilters($query);
        })->sum('value');
    
        $totalEarningFreeVoucher = Earning::where('type', 'Free Voucher')->where(function ($query) use ($applyFilters) {
            $applyFilters($query);
        })->sum('value');
    
        $totalDebitFreeVoucher = Debit::where('type', 'Free Voucher')->where(function ($query) use ($applyFilters) {
            $applyFilters($query);
        })->sum('value');
    
    
        
          
        
        return view('admin.wallets', compact(
            'user', 'title', 'wallets', 'totalEarningCash', 'totalDebitCash', 
            'totalEarningPoints', 'totalLeftPoint', 'totalRightPoint', 
            'totalEarningVoucher', 'totalDebitVoucher', 
            'totalEarningFreeVoucher', 'totalDebitFreeVoucher'
        ));
    }
    
    private function exportToCSV($debits, $earnings, $applyFilters)
    { 
        // Apply filters to the queries again (for all data, not paginated)
        $applyFilters($debits);
        $applyFilters($earnings);
    
        // Combine filtered queries to get all results
        $allWallets = DB::table(DB::raw("({$debits->union($earnings)->toSql()}) as combined"))
            ->mergeBindings($debits->getQuery())
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Prepare the output as a CSV string
        $output = fopen('php://output', 'w');
    
        // Set headers for the download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="earnings.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
    
        // Define the headers
        $headers = ['ID', 'MEMBER', 'TRANSACTION TYPE', 'WALLET TYPE', 'VALUE', 'DESCRIPTION', 'DATE'];
        fputcsv($output, $headers);
    
        // Loop through the combined wallet transactions
        foreach ($allWallets as $index => $wallet) {
            // Fetch the member details
            $member = Member::find($wallet->member_id);
    
            // Prepare the row with formatted values
            $row = [
                $index + 1,  // Use index + 1 for ID
                $member ? $member->username : 'Unknown',  // Handle case where member might not be found
                strtoupper($wallet->transaction_type),
                $wallet->type,
                
                $this->formatValue($wallet->value),
                $wallet->description,
                $wallet->created_at,
            ];
            fputcsv($output, $row);
        }
    
        // Close the output stream
        fclose($output);
        exit;  // Stop further script execution
    }


    private function formatValue($value)
    {
        // Check if the value is numeric
        if (is_numeric($value)) {
            // If it is a float, format it as a string
            if (strpos((string)$value, '.') !== false) {
                return number_format((float)$value, 2, '.', '');
            }
            // If it's an integer or string with leading zeros, return as is
            return (string)$value;
        }
        
        // If not numeric, return it directly as a string
        return (string)$value;
    }

    
    public function deleteWallet($encryptedId, $encryptedTransactionType)
    {
        // Decrypt the ID and transaction type
        $id = Crypt::decrypt($encryptedId);
        $transactionType = Crypt::decrypt($encryptedTransactionType);
    
        // Determine which table to delete from
        if ($transactionType === 'debit') {
            // Find and delete from the debits table
            Debit::where('id', $id)->delete();
        } elseif ($transactionType === 'earning') {
            // Find and delete from the earnings table
            Earning::where('id', $id)->delete();
        } else {
            // Handle invalid transaction type
            return redirect()->back()->with('error', 'Invalid transaction type.');
        }
    
        return redirect()->back()->with('success', 'Record deleted successfully.');
    }
    
    public function deleteMultipleWallets(Request $request)
    {
        $request->validate([
            'wallets' => 'required',
        ]);
        
        $wallets = $request->input('wallets');
    
        if ($wallets) {
            foreach ($wallets as $wallet) {
                // Split the value to get id and transaction_type
                list($id, $transactionType) = explode(':', $wallet);
    
                // Determine which table to delete from based on transaction type
                if ($transactionType === 'debit') {
                    // Delete from the debits table
                    Debit::where('id', $id)->delete();
                } elseif ($transactionType === 'earning') {
                    // Delete from the earnings table
                    Earning::where('id', $id)->delete();
                } else {
                    // Handle invalid transaction type
                    return redirect()->back()->with('error', 'Invalid transaction type.');
                }
            }
    
            return redirect()->back()->with('success', 'Selected records have been deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'No records were selected for deletion.');
        }
    }



    public function creditmember(){
        $user = Auth::guard('admin')->user();
        $title = "Credit Member";

        if ($user) {
            $title = "Credit Member"; 
            $members = Member::all();

            return view('admin.addcredit', compact('title', 'user', 'members'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function addearnings(Request $request){
        $request->validate([
            'member'      => 'required|integer',
            'type'        => 'required|string|max:255',
            'value'       => 'required|integer',
        ]);

        $member = Member::find($request->member);

        if(isset($member) && !empty($member)){

            $desc = "Credited by System Administrator";
            $value = $request->value;
            $type = $request->type;

            $member->addEarnings($value, $type, $desc);
            

            return redirect('admin/earnings')->with('success', 'Member has been credited successfully.');
        }

        return back()->with('error', 'Error in crediting member.');
    }
    
    
    public function debitmember(){
        $user = Auth::guard('admin')->user();
        $title = "Debit Member";

        if ($user) {
            $title = "Debit Member"; 
            $members = Member::all();

            return view('admin.debitmember', compact('title', 'user', 'members'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }
    
    public function debitearnings(Request $request){
        $request->validate([
            'member'      => 'required|integer',
            'type'        => 'required|string|max:255',
            'value'       => 'required|integer',
        ]);

        $member = Member::find($request->member);

        if(isset($member) && !empty($member)){

            $desc = "Debited by System Administrator";
            $value = $request->value;
            $type = $request->type;

            $member->addDebit($value, $type, $desc);
            

            return redirect('admin/earnings')->with('success', 'Member has been debited successfully.');
        }

        return back()->with('error', 'Error in debiting member.');
    }
    
    

    public function createpickup(Request $request){

        $request->validate([
            'member'      => 'required|integer',
            'type'        => 'required|string|max:255',
        ]);

        $pickup = new Pickup();
        $pickup->member_id  = $request->member;
        $pickup->type       = $request->type;
        $pickup->status     = 1;
        $pickup->save();

        return redirect('admin/pickup')->with('success', 'Pickup location has been created successfully.');
    }

    public function deletepickup($id){
        $id = Crypt::decrypt($id);
        $data = Pickup::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Pickup location has been deleted successfully.');
        }
    }

    public function deactivatepickup($id){
        $user = Auth::guard('admin')->user();


        if ($user) {
            if($id != NULL){
                $id = Crypt::decrypt($id);
                $pickup = Pickup::where('id', $id)->first();

                if($pickup && !empty($pickup)){
                    $pickup->status  = 0;
                    $pickup->update();
                    return back()->with('success', 'Pickup location has been updated successfully.');
                }
            }
        }

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function activatepickup($id){
        $user = Auth::guard('admin')->user();


        if ($user) {
            if($id != NULL){
                $id = Crypt::decrypt($id);
                $pickup = Pickup::where('id', $id)->first();

                if($pickup && !empty($pickup)){
                    $pickup->status  = 1;
                    $pickup->update();
                    return back()->with('success', 'Pickup location has been updated successfully.');
                }
            }
        }

        return redirect('admin')->with('error', 'Unauthorized access.');
    }


    public function stockistpackages(){
        $user = Auth::guard('admin')->user();

        if ($user) {
            $packages = StockistPackage::OrderBy('id', "ASC")->get();
            $title = "Manage Stickist Packages";
            return view('admin.stockistpackages', compact('title', 'user', 'packages'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function newstokistpackage($id=null){
        $user = Auth::guard('admin')->user();

        if ($user) {
            if($id != NULL){
                $title = "Update Package";
                $id = Crypt::decrypt($id);
                $data = StockistPackage::Find($id);
                $packages = StockistPackage::all();

                return view('admin.newstockistpackage', compact('title', 'user', 'data', 'packages'));
            }else{
                $title = "Add Package"; 
                return view('admin.newstockistpackage', compact('title', 'user'));
            }
        }

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function createstockistpackage(Request $request){

        $request->validate([
            'name'          => 'required|string|max:255',
            'position'      => 'required|integer|max:5',
            'commission'    => 'required|integer',
            'rcommission'   => 'nullable|integer',
            'rscommission'  => 'nullable|integer',
            'scommission'   => 'nullable|integer',
        ]);

        $package                                = new StockistPackage();
        $package->name                          = $request->name;
        $package->commission                    = $request->commission;
        $package->position                      = $request->position;
        $package->restock_commission            = $request->rcommission;
        $package->pickup_restock_commission     = $request->rscommission;
        $package->sponsor_commission            = $request->scommission;
        

        $package->save();

        return redirect('admin/stockistpackages')->with('success', 'Package has been created successfully.');
    }

    public function deletestockistpackage($id){
        $id = Crypt::decrypt($id);
        $data = StockistPackage::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Package has been deleted successfully.');
        }
    }

    public function updatestockistpackage(Request $request){
        $request->validate([
            'id'            => 'required|integer',
            'name'          => 'required|string|max:255',
            'position'      => 'required|integer',
            'commission'    => 'required|numeric',
            'rcommission'   => 'nullable|numeric',
            'rscommission'  => 'nullable|numeric',
            'scommission'   => 'nullable|numeric',
        ]);

        $product = StockistPackage::where('id', $request->id)->first();

        if($product && !empty($product )){
            $product->name                          = $request->name;
            $product->commission                    = $request->commission;
            $product->position                      = $request->position;
            $product->restock_commission            = $request->rcommission;
            $product->pickup_restock_commission     = $request->rscommission;
            $product->sponsor_commission            = $request->scommission;
            $product->update();
            
            return back()->with('success', 'Product has been updated successfully.');
        }

        return back()->with('error', 'Error updating product.');
    }


    public function packages(Request $request)
    {  
        $user = Auth::guard('admin')->user();

        if ($user) {
            $packages = Package::OrderBy('id', "DESC")->get();
            $title = "Manage Packages";
            return view('admin.packages', compact('title', 'user', 'packages'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function newpackage($id=null)
    {  
        $user = Auth::guard('admin')->user();


        if ($user) {
            if($id != NULL){
                $title = "Update Package";
                $id = Crypt::decrypt($id);
                $data = Package::Find($id);
                return view('admin.newpackage', compact('title', 'user', 'data'));
            }else{
                $title = "Add Package"; 
                return view('admin.newpackage', compact('title', 'user'));
            }
        }

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function createpackage(Request $request){

        $request->validate([
            'name'      => 'required|string|max:255',
            'price'     => 'required|string|max:10',
            'actualpv'  => 'required|string|max:10',
            'bonuspv'   => 'required|string|max:10',
            'content'   => 'nullable|string|max:255',
            'voucher'   => 'nullable|string|max:255',
        ]);

        $package = new Package();
        $package->name          = $request->name;
        $package->price         = $request->price;
        $package->actual_pv     = $request->actualpv;
        $package->bonus_pv      = $request->bonuspv;
        $package->free_voucher  = $request->voucher;
        $package->content       = $request->content;
        $package->status        = 1;

        $package->save();

        return redirect('admin/packages')->with('success', 'Package has been created successfully.');
    }

    

    public function updatepackage(Request $request){
        $request->validate([
            'name'      => 'required|string|max:255',
            'price'     => 'required|string|max:10',
            'actualpv'  => 'required|string|max:10',
            'bonuspv'   => 'required|string|max:10',
            'voucher'   => 'nullable|string|max:255',
            'content'   => 'nullable|string|max:255',
        ]);

        $package = Package::find($request->id);

        if($package && !empty($package)){
            $package->name          = $request->name;
            $package->price         = $request->price;
            $package->actual_pv     = $request->actualpv;
            $package->bonus_pv      = $request->bonuspv;
            $package->free_voucher  = $request->voucher;
            $package->content       = $request->content;
            $package->status        = 1;

            $package->update();

            return redirect('admin/packages')->with('success', 'Package has been updated successfully.');
        }

        return redirect('admin/packages')->with('error', 'Error updating package.');
    }

    public function deletepackage($id){
        $id = Crypt::decrypt($id);
        $data = Package::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Package has been deleted successfully.');
        }
    }
    

    public function tokens(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $title = "Manage Access Tokens";

        if ($user) {
            if($request->has('_token')){ 

                $request->validate([
                    'search'     => 'required|string|max:5',
                ]);

                $query = $request->all();
                $tokens = AccessToken::where('token', 'LIKE', '%' . $request->search . '%')
                                    ->OrderBy('status',"ASC")
                                    ->OrderBy('id', "DESC")
                                    ->paginate(10);

                return view('admin.tokens', compact('title', 'user', 'tokens'));

            }

            $tokens = AccessToken::OrderBy('status',"ASC")
                                ->OrderBy('id', "DESC")
                                ->paginate(10);

            return view('admin.tokens', compact('title', 'user', 'tokens'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function newtoken()
    {  
        $user = Auth::guard('admin')->user();


        if ($user) {
            $title = "Add Access Token"; 
            return view('admin.newtoken', compact('title', 'user'));
        }

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function generatetoken(Request $request){

        $request->validate([
            'price'     => 'required|string',
            'no'        => 'required|integer',
        ]);

        $no = $request->no;

        for ($i = 1; $i <= $no; $i++) {
            $accesstoken = $this->RandomString(16);
            $token = new AccessToken();
            $token->token         = $accesstoken;
            $token->amount        = $request->price;
            $token->status        = 0;

            $token->save();
        }

        return redirect('admin/tokens')->with('success', 'Access tokens has been created successfully.');
    }

    public function deletetoken($id){
        $id = Crypt::decrypt($id);
        $data = AccessToken::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Access token has been deleted successfully.');
        }
    }
    
    public function currency(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $title = "Currency Settings";

        if ($user) {
            if($request->has('_token')){ 

                $request->validate([
                    'search'     => 'required|string|max:5',
                ]);

                $query = $request->all();
                $currencies = Currency::whereJsonContains('country', 'LIKE', '%' . $request->search . '%')->get();

                return view('admin.currency', compact('title', 'user', 'currencies'));

            }

            $currencies = Currency::all();

            return view('admin.currency', compact('title', 'user', 'currencies'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function newcurrency()
    {  
        $user = Auth::guard('admin')->user();


        if ($user) {
            $title = "Add Currency";
            $countries = Country::all();

            return view('admin.newcurrency', compact('title', 'user', 'countries'));
        }

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function createcurrency(Request $request){

        $request->validate([
            'currency'   => 'required|string|max:10',
            'symbol'     => 'required|string|max:10',
            'country'    => 'required|array', // ensure stores is an array
            'country.*'  => 'required|integer',
            'price'      => 'required|string|max:5',
        ]);

        $country = $request->input('country');

        // Ensure all stores IDs are strings (if needed)
        $country = array_map('strval', $country);


        $Currency = new Currency();
        $Currency->currency     = $request->currency;
        $Currency->symbol       = $request->symbol;
        $Currency->country      = json_encode($country);
        $Currency->conversion   = $request->price;

        $Currency->save();
        

        return redirect('currency')->with('success', 'Curency has been created successfully.');
    }

    public function deletecurrency($id){
        $id = Crypt::decrypt($id);
        $data = Currency::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Currency has been deleted successfully.');
        }
    }
    

    public function orders(){
        $user = Auth::guard('admin')->user();
        $title = "All Orders";
        $orders = Order::OrderBy('id', "DESC")->OrderBy('member_id')->paginate(10);
        
        return view('admin.orders', ['title' => $title, 'user' => $user, 'orders' => $orders]);
    }

    public function processorder($id){
        $id = Crypt::decrypt($id);
        $data = Order::where('id',$id)->first();
        
        if($data){
            $data->status = 2;
            $data->update();
            
            return back()->with('success', 'Order has been updated successfully.');
        }
    }
    
    public function deleteorder($id)
    {
        try {
            // Decrypt the order ID
            $id = Crypt::decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return back()->with('error', 'Invalid order ID.');
        }
    
        // Find the order by ID
        $data = Order::find($id);
    
        if ($data) {
            $transactionId = $data->transaction_id;
    
            // Find the transaction by ID
            $transaction = Transaction::find($transactionId);
    
            if ($transaction && !empty($transaction->check_id)) {
                // Begin a transaction to ensure all or nothing is deleted
                DB::beginTransaction();
    
                try {
                    // Delete related earnings and debits
                    Debit::where('transaction_id', $transaction->check_id)->delete();
                    Earning::where('transaction_id', $transaction->check_id)->delete();
    
                    // Delete the order and transaction
                    $data->delete();
                    $transaction->delete();
    
                    // Commit the transaction
                    DB::commit();
    
                    return back()->with('success', 'Order, related earnings, and debits have been deleted.');
                } catch (\Exception $e) {
                    // Rollback the transaction if any delete fails
                    DB::rollBack();
                    Log::error('Failed to delete order or related records: ' . $e->getMessage());
                    return back()->with('error', 'Failed to delete order or related records.');
                }
            } else {
                return back()->with('error', 'Related transaction not found or missing check ID.');
            }
        }
    
        return back()->with('error', 'Order not found.');
    }


    public function withdrawals(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $title = "Withdrawals";
        $desc = "Withdrawal Request";
        
        // Validate the input data
        $request->validate([
            'from'          => 'nullable|date|before_or_equal:today', 
            'to'            => 'nullable|date|after_or_equal:from|before_or_equal:today', 
            'username'      => 'nullable|string', 
        ]);
    
        // Get the fromdate and todate from the request, if available
        $fromdate = $request->input('from');
        $todate = $request->input('to');
        $username = $request->input('username');
    
        // Initialize the query for fetching debits and applying filters
        $debitsQuery = Debit::where('type', 'Cash')
            ->where('description', 'LIKE', '%' . e($desc) . '%');
    
        // Apply date filters if provided
        if ($fromdate && $todate) {
            $debitsQuery->whereBetween('created_at', [$fromdate, $todate]);
        }
        
        // Apply date filters if provided
        if ($username) {
            $member = Member::where('username', $request->username)->first();
            if ($member) {
                $query->where('member_id', $member->id);
            }
        }
    
        // Clone the query to calculate the sum of the value column
        $totalValue = (clone $debitsQuery)->sum('value');
    
        // Paginate the result
        $wallets = $debitsQuery->orderBy('created_at', 'desc')->paginate(10);
    
        return view('admin.withdrawals', compact('user', 'title', 'wallets', 'totalValue'));
    }
    
    public function deletewithdrawal($id){
        $id = Crypt::decrypt($id);
        $data = Debit::where('id',$id)->where('description', "Withdrawal Request")->first();
        
        if($data){
            $rand = $data->transaction_id;
            if(!empty($data)){
                $charges = Debit::where('transaction_id',$rand)->where('type', "Withdrawal Charges")->delete();
                $data->delete();
                return back()->with('success', 'Withdrawal request has been declined.');
            }
        
            return back()->with('error', 'Error in declining request.');
        }
    }
    
    public function processrequest(Request $request){
        
        $request->validate([
            'wallets' => 'required',
        ]);
        
        $wallets = $request->input('wallets');
        $requests = []; $x = 0;
        
        $filename = 'bcm_withdrawal_request.csv';

        // Open a file in write mode ('w')
        $file = fopen($filename, 'w');
        
        // Define the headers
        $headers = ['Id', 'Destination Bank Code', 'Destination Bank Name', 'Account No', 'Account Name', 'Amount', 'Currency', 'Narration', 'Reference No'];
        
        // Write the headers to the CSV file
        fputcsv($file, $headers);

    
        if ($wallets) {
            foreach ($wallets as $wallet) {
                $x++;
                // Split the value to get id and transaction_type
                list($id, $transactionId) = explode(':', $wallet);
            
                $data = Debit::where('id',$id)->where('description', "Withdrawal Request")->first();
                
                // Determine which table to delete from based on transaction type
                if ($data && !empty($data)) {
                    $member =  Member::where('id', $data->member_id)->first();
                    $bank = Bank::where('id', $member->bankname)->first();
                    
                    if($bank && !empty($bank)){
                        //$requests[] = @array($x, $bank->code, $bank->bankname, $member->bankaccount, $member->name, $data->value, "NGN", $data->transaction_id);
                        
                        $requests = [
                                        $x,
                                        $bank->code,
                                        $bank->bankname,
                                        $member->bankaccount,
                                        $member->name,
                                        $this->formatValue($data->value),
                                        "NGN",
                                        "BCM WITHDRAWAL REQUEST",
                                        $transactionId,
                                    ];
                                    
                        // Write the row data to the CSV file
                        fputcsv($file, $requests);
            
                        //update transaction to processed
                        $data->processed = 1;
                        $data->update();
                    }
                } else {
                    // Handle invalid transaction type
                    return redirect()->back()->with('error', 'Invalid transaction type.');
                }
            }
    
            // Close the file after writing
            fclose($file);
            
            // Provide a download link or redirect to the CSV file
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            readfile($filename);
        } else {
            return redirect()->back()->with('error', 'Error in processing request.');
        }
        
        
    }


    public function banners(Request $request)
    {  
        $user = Auth::guard('admin')->user();
        $title = "Manage Banners";

        if ($user) {
            $banners = Banner::all();
            return view('admin.banners', compact('title', 'user', 'banners'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function newbanner()
    {  
        $user = Auth::guard('admin')->user();
        if ($user) {
            $title = "Add Banner"; 
            return view('admin.newbanner', compact('title', 'user'));
        
        }
        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function createbanner(Request $request){


        $request->validate([
            'title'     => 'required|string|max:255',
            'image'     => 'required|image|mimes:jpg,jpeg,png|max:2048', // restrict to jpg, jpeg, png with max size 2048KB
            
        ]);

        $banner = new Banner();
        $banner->name   = $request->title;

        //check if feature image was uploaded and save it
        //save image
        if($request->hasFile('image')){
            $rand = $this->RandomString(8);
            $filename = $rand.'.'.$request->image->extension();
            $request->image->move('front/assets/images/banners', $filename);
            $banner->image = $filename;
        }     

        $banner->save();

        return redirect('admin/banners')->with('success', 'Banners has been created successfully.');
    }


    public function deletebanner($id){
        $id = Crypt::decrypt($id);
        $data = Banner::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Banner has been deleted successfully.');
        }
    }
    
    
    public function banks(){
        $user = Auth::guard('admin')->user();
        
        

        if ($user) {
            $title = "Manage Banks";
            $banks = Bank::all();
            
            return view('admin.banks', compact('title', 'user', 'banks'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }
    
    public function newbank()
    {  
        $user = Auth::guard('admin')->user();
        if ($user) {
            $title = "Add Bank"; 
            return view('admin.newbank', compact('title', 'user'));
        
        }
        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function createbank(Request $request){


        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|integer',
            
        ]);

        $bank = new Bank();
        $bank->bankname = $request->name;
        $bank->code     = $request->code;

        $bank->save();

        return redirect('admin/banks')->with('success', 'Bank has been created successfully.');
    }


    public function deletebank($id){
        $id = Crypt::decrypt($id);
        $data = Bank::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Bank has been deleted successfully.');
        }
    }
    
    public function directreferralbonus(){
        $title = "Direct Referral Bonus Setting";
        $user = Auth::guard('admin')->user();

        if($user){

            $packages = Package::all();
            $bonusRates = DB::table('package_bonus_rates')
                            ->orderBy('package_id', 'asc')
                            ->orderBy('level', 'asc')  
                            ->get();

            return view('admin.directreferralbonus', compact('title', 'user', 'bonusRates','packages'));
        }

        return back()->with('error', 'Access denied.');
    }
    
    public function addDirectReferralBonus(Request $request){
        $user = Auth::guard('admin')->user();
        
        $validated = $request->validate([
            'package'   => 'required|integer|exists:packages,id', 
            'bonus'     => 'required|numeric', 
            'level'     => 'required|integer|between:1,10', 
        ]);
        
        if($user->role == "superadmin" OR $user->role == "manager"){ 

            $bonusRate = new \App\Models\PackageBonusRate(); 

            // Assign the validated values to the model
            $bonusRate->package_id = $validated['package'];
            $bonusRate->level = $validated['level'];
            $bonusRate->bonus_rate = $validated['bonus'];
        
            // Step 3: Save the record
            $bonusRate->save();
    
            return redirect('admin/directreferralbonus')->with('status', 'Bonus created successfully!');
        }
    }
    
    public function deletedirectreferral($id){
        $id = Crypt::decrypt($id);
        $data = DB::table('package_bonus_rates')->where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Bonus has been deleted successfully.');
        }
    }
    

    public function settings(){
        $title = "Website Settings";
        $user = Auth::guard('admin')->user();

        if($user){

            $data = Setting::all()->keyBy('id');
            $categories = Category::all();

            return view('admin.settings', compact('title', 'user', 'data', 'categories'));
        }

        return back()->with('error', 'Access denied.');
    }

    public function updatesettings(Request $request){
        $request->validate([
            'image'     => 'nullable',
            'phone1'    => 'nullable|string|max:255',
            'phone2'    => 'nullable|string|max:255',
            'email1'    => 'nullable|string|max:255',
            'email2'    => 'nullable|string|max:255',
            'address'   => 'nullable|string|max:255',
            'fb'        => 'nullable|string|max:255',
            'tw'        => 'nullable|string|max:255',
            'li'        => 'nullable|string|max:255',
            'yt'        => 'nullable|string|max:255',
            'in'        => 'nullable|string|max:255',
            'refBonus'  => 'nullable|string|max:255',
            'pbonus'    => 'nullable|string|max:255',
            'bonus10'   => 'nullable|string|max:255',
            'bonus5'    => 'nullable|string|max:255',
            'dBonus'    => 'nullable|string|max:255',
            
        ]);

        $user = Auth::guard('admin')->user();

        if($user){
            $setting1 = Setting::where('id', "1")->first();
            if(isset($setting1) && !empty($setting1)){
                if($request->hasFile('image')){ 
                    //save user profile image 
                    $rand = Carbon::now()->format('YmdHis');
                    $filename = $rand.'.'.$request->image->extension();
                    $request->image->move('front/assets/images', $filename);
                    $setting1->content = $filename;
                    $setting1->update();
                } 
            }else{
                $settingC1           = new Setting();
                $settingC1->name     = "Logo";
                if($request->hasFile('image')){ 
                    //save user profile image 
                    $rand = Carbon::now()->format('YmdHis');
                    $filename = $rand.'.'.$request->image->extension();
                    $request->image->move('front/assets/images', $filename);
                    $settingC1->content = $filename;
                } 
                $settingC1->save();
            }


            $setting2 = Setting::where('id', "2")->first();
            if(isset($setting2) && !empty($setting2)){
                $setting2->content = $request->phone1;
                $setting2->update();
                
            }else{
                $settingC2              = new Setting();
                $settingC2->name        = "Phone Number 1";
                $settingC2->content     = $request->phone1;
                $settingC2->save();
            }


            $setting3 = Setting::where('id', "3")->first();
            if(isset($setting3) && !empty($setting3)){
                $setting3->content = $request->phone2;
                $setting3->update();
                
            }else{
                $settingC3              = new Setting();
                $settingC3->name        = "Phone Number 2";
                $settingC3->content     = $request->phone2;
                $settingC3->save();
            }

            $setting4 = Setting::where('id', "4")->first();
            if(isset($setting4) && !empty($setting4)){
                $setting4->content = $request->email1;
                $setting4->update();
                
            }else{
                $settingC4              = new Setting();
                $settingC4->name        = "Email 1";
                $settingC4->content     = $request->email1;
                $settingC4->save();
            }

            $setting5 = Setting::where('id', "5")->first();
            if(isset($setting5) && !empty($setting5)){
                $setting5->content = $request->email2;
                $setting5->update();
                
            }else{
                $settingC5              = new Setting();
                $settingC5->name        = "Email 2";
                $settingC5->content     = $request->email2;
                $settingC5->save();
            }

            $setting6 = Setting::where('id', "6")->first();
            if(isset($setting6) && !empty($setting6)){
                $setting6->content = $request->address;
                $setting6->update();
                
            }else{
                $settingC6              = new Setting();
                $settingC6->name        = "Address";
                $settingC6->content     = $request->address;
                $settingC6->save();
            }

            $setting7 = Setting::where('id', "7")->first();
            if(isset($setting7) && !empty($setting7)){
                $setting7->content = $request->fb;
                $setting7->update();
                
            }else{
                $settingC7              = new Setting();
                $settingC7->name        = "Facebook Link";
                $settingC7->content     = $request->fb;
                $settingC7->save();
            }

            $setting8 = Setting::where('id', "8")->first();
            if(isset($setting8) && !empty($setting8)){
                $setting8->content = $request->tw;
                $setting8->update();
                
            }else{
                $settingC8              = new Setting();
                $settingC8->name        = "Twitter Link";
                $settingC8->content     = $request->tw;
                $settingC8->save();
            }


            $setting9 = Setting::where('id', "9")->first();
            if(isset($setting9) && !empty($setting9)){
                $setting9->content = $request->li;
                $setting9->update();
                
            }else{
                $settingC9              = new Setting();
                $settingC9->name        = "LinkedIn Link";
                $settingC9->content     = $request->li;
                $settingC9->save();
            }

            $setting10 = Setting::where('id', "10")->first();
            if(isset($setting10) && !empty($setting10)){
                $setting10->content = $request->yt;
                $setting10->update();
                
            }else{
                $settingC10              = new Setting();
                $settingC10->name        = "Youtube Link";
                $settingC10->content     = $request->yt;
                $settingC10->save();
            }

            $setting11 = Setting::where('id', "11")->first();
            if(isset($setting11) && !empty($setting11)){
                $setting11->content = $request->in;
                $setting11->update();
                
            }else{
                $settingC11              = new Setting();
                $settingC11->name        = "Instagram Link";
                $settingC11->content     = $request->in;
                $settingC11->save();
            }

            $setting12 = Setting::where('id', "12")->first();
            if(isset($setting12) && !empty($setting12)){
                $setting12->content = $request->refBonus;
                $setting12->update();
                
            }else{
                $settingC12              = new Setting();
                $settingC12->name        = "Discount Referrer Bonus";
                $settingC12->content     = $request->refBonus;
                $settingC12->save();
            }
            
            
            $setting13 = Setting::where('id', "13")->first();
            if(isset($setting13) && !empty($setting13)){
                $setting13->content = $request->pbonus;
                $setting13->update();
                
            }else{
                $settingC13              = new Setting();
                $settingC13->name        = "Personal Discount Bonus";
                $settingC13->content     = $request->pbonus;
                $settingC13->save();
            }
            
            
            $setting14 = Setting::where('id', "14")->first();
            if(isset($setting14) && !empty($setting14)){
                $setting14->content = $request->Bonus10;
                $setting14->update();
                
            }else{
                $settingC14              = new Setting();
                $settingC14->name        = "10% instant cash back";
                $settingC14->content     = $request->Bonus10;
                $settingC14->save();
            }
            
            
            $setting15 = Setting::where('id', "15")->first();
            if(isset($setting15) && !empty($setting15)){
                $setting15->content = $request->bonus5;
                $setting15->update();
                
            }else{
                $settingC15              = new Setting();
                $settingC15->name        = "5% instant cash back";
                $settingC15->content     = $request->bonus5;
                $settingC15->save();
            }
            
            $setting16 = Setting::where('id', "16")->first();
            if(isset($setting16) && !empty($setting16)){
                $setting16->content = $request->dBonus;
                $setting16->update();
                
            }else{
                $settingC16              = new Setting();
                $settingC16->name        = "Direct Referral Bonus";
                $settingC16->content     = $request->dBonus;
                $settingC16->save();
            }

            return redirect('/admin/settings')->with('success', 'Website settings has been updated successfully.');
            
        }
        
        return back()->with('error', 'Access denied.');

    }


    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin');
    }
    
    public function admins(){
        $user = Auth::guard('admin')->user();
        
        if ($user && $user->role == "superadmin") {
            $title = "Manage Admins";
            //dd($user);
            
            $admins = Admin::all(); 
            
            return view('admin.admins', compact('title', 'user', 'admins'));
        }
        
        return redirect('admin')->with('error', 'Unauthorized access.');
    }
    
    public function newadmin($id=null)
    {  
        $user = Auth::guard('admin')->user();


        if ($user) {

            if($id != NULL){
                $title = "Update Admin";
                $id = Crypt::decrypt($id);
                $data = Admin::Find($id);
                return view('admin.newadmin', compact('title', 'user', 'data'));
            }else{
                $title = "Add Admin"; 
                return view('admin.newadmin', compact('title', 'user'));
            }
            
            
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    

    public function createAdmin(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:admins',
            'phone'     => 'required|max:255',
            'password'  => 'required|string|min:8|confirmed',
            'roles'     => 'required|string',
        ]);
        
        if($user->role == "superadmin" OR $user->role == "manager"){ 

            Admin::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'password'  => Hash::make($request->password),
                'role'      => $request->roles,
            ]);
    
            return redirect('admin/admins')->with('status', 'Admin created successfully!');
        }
    }
    
    
    public function updateadmin(Request $request){
        
        $user = Auth::guard('admin')->user();
        
        $request->validate([
            'id'        => 'required|integer',
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255',
            'phone'     => 'required|max:255',
            'password'  => 'nullable|string|min:8',
            'roles'     => 'required|string',
        ]);
        
        $admin = Admin::find($request->id);
        
        if($admin && !empty($admin) && ($user->role == "superadmin" OR $user->role == "manager")){ 
            if(isset($request->password) && !empty($request->password)){
                $admin->password = Hash::make($request->password);
            }
            
            $admin->name      = $request->name;
            $admin->email     = $request->email;
            $admin->phone     = $request->phone;
            $admin->role      = $request->roles;
            
            $admin->update();
            
            return redirect('admin/admins')->with('status', 'Admin updated successfully!');
            
        }
        
        return redirect('admin')->with('error', 'Invalid selection.');
        
    }
    
    public function deleteadmin($id){
        $id = Crypt::decrypt($id);
        $data = Admin::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'User has been deleted successfully.');
        }
    }

    public function members(Request $request)
    {  
        $user = Auth::guard('admin')->user();
    
        if ($user) {
            // Define validation rules
            $rules = [
                'rank'      => 'nullable|in:Elite,Royal Diamond,Crown Diamond,Sapphire,Emerald,Ruby,Diamond,Platinum,Gold,Silver,Bronze,No Rank',
                'package'   => 'nullable|exists:packages,id',
                'sponsor'   => 'nullable|string|max:255',
                'member'    => 'nullable|string|max:255',
                'from'      => 'nullable|date|before_or_equal:to',
                'to'        => 'nullable|date|after_or_equal:from',
                'country'   => 'nullable|exists:countries,id',
            ];
    
            // Validate the request
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
    
            // Retrieve validated input values
            $rank = $request->input('rank');
            $package = $request->input('package');
            $sponsor = $request->input('sponsor');
            $username = $request->input('member');
            $from = $request->input('from');
            $to = $request->input('to');
            $country = $request->input('country');
            
            // Initialize query
            $query = Member::query();
            
            // Apply filters based on search parameters
            if ($rank) {
                // Filter by rank
                $query->where(function ($q) use ($rank) {
                    $q->whereRaw('members.id IN (
                        SELECT member_id FROM earnings 
                        WHERE type = "Points" 
                        GROUP BY member_id 
                        HAVING SUM(value) >= ? 
                        AND SUM(value) < ? 
                    )', $this->getRankPointsRange($rank));
                });
            }
    
            if ($package) {
                // Filter by package
                $query->whereHas('packages', function ($q) use ($package) {
                    $q->where('packages.id', $package);
                });
            }
    
            if ($sponsor) {
                // Fetch the user ID by sponsor username
                $sponsorMember = Member::where('username', $sponsor)->first();
    
                if ($sponsorMember) {
                    // Use the user ID for filtering
                    $query->where('referrer_id', $sponsorMember->id);
                } else {
                    // No member found with the given username, return empty result
                    $query->where('referrer_id', null); // This will ensure no results are returned if the sponsor is not found
                }
            }
    
            if ($from && $to) {
                // Filter by registration date range
                $query->whereBetween('created_at', [$from, $to]);
            }
            
            if ($username) {
                // Filter by country
                $query->where('username', $username);
            }
    
            if ($country) {
                // Filter by country
                $query->where('country_id', $country);
            }
    
            // Order by ID descending and paginate results
            $members = $query->orderBy('id', 'DESC')->paginate(10)->appends($request->except('page'));
            
            $title = "Manage Members";
            $packages = Package::all();
            $countries = Country::all();
            
            return view('admin.members', compact('title', 'user', 'members', 'packages', 'countries'));
        }
        
        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    
    private function getRankPointsRange($rank)
    {
        $ranges = [
            'Elite' => [35000000, PHP_INT_MAX],
            'Royal Diamond' => [17000000, 34999999],
            'Crown Diamond' => [8000000, 16999999],
            'Sapphire' => [3500000, 7999999],
            'Emerald' => [1400000, 3499999],
            'Ruby' => [555000, 1399999],
            'Diamond' => [185000, 554999],
            'Platinum' => [70000, 184999],
            'Gold' => [28000, 69999],
            'Silver' => [5000, 27999],
            'Bronze' => [1500, 4999],
            'No Rank' => [0, 1499],
        ];
    
        return $ranges[$rank] ?? [0, PHP_INT_MAX];
    }
    
    public function membertree($id){
        $member = Member::find($id);
        $user = Auth::guard('admin')->user();
        
        if($member){
            $title = $member->name;
            return view('admin.membertree', compact('title', 'user', 'id', 'member'));
        }
        
        return back()->with('error', 'Invalid selection.');
    }

    public function deletemember($id)
    {
        $id = Crypt::decrypt($id);
        $data = Member::where('id', $id)->first();
    
        if ($data) {
            // Find and update dependent records
            Member::where('left_leg_id', $id)->update(['left_leg_id' => null]);
            Member::where('right_leg_id', $id)->update(['right_leg_id' => null]);
    
            // Delete related data in MembersPackage table
            MembersPackage::where('member_id', $id)->delete();
    
            // Delete the member
            $data->delete();
    
            return back()->with('success', 'Member has been deleted successfully.');
        }
        return back()->with('error', 'Member not found.');
    }

    
    public function viewmember($id){
        $id = Crypt::decrypt($id);
        $data = Member::where('id',$id)->first();
        $user = Auth::guard('admin')->user();
        $countries = Country::all();
        
        if($data){
            $title = $data->name;
            return view('admin.memberprofile', compact('title', 'user', 'data', 'countries'));
        }
        
        return back()->with('error', 'Invalid selection.');
    }
    
    public function updatememberprofile(Request $request){ 
        
        $request->validate([
            'id'            => 'required|integer',
            'name'          => 'required|string',
            'username'      => 'required|string',
            'sponsor'       => 'required|string',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // restrict to jpg, jpeg, png with max size 2048KB
            'phone'         => 'required',
            'email'         => 'required|string',
            'country'       => 'required|integer',
            'address'       => 'nullable|string',
            'password'      => 'nullable|string',
        ]);

        $user = Member::find($request->id); 
        
        if($user && !empty($user)){
            $user->name     = $request->name;
            $user->username = $request->username;
            $user->phone    = $request->phone;
            $user->email    = $request->email;
            $user->country  = $request->country;
            $user->address  = $request->address;
            
            $sponsor = Member::where('username', $request->sponsor)->first();
            if($sponsor && !empty($sponsor)){
                $user->referrer_id = $sponsor->id;
            }
            
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
    
    public function updatebinaryplacement(Request $request){
        $request->validate([
            'id'        => 'required|integer',
            'placement' => 'required|in:left,right',
            'binary'    => 'required',
        ]);
        
        //get old placement
        $memberId = $request->id;

        // Update all members where `left_leg_id` or `right_leg_id` matches the given ID
        Member::where('left_leg_id', $memberId)
            ->update(['left_leg_id' => null]);
        
        Member::where('right_leg_id', $memberId)
            ->update(['right_leg_id' => null]);
            
            
        //replace member on tree based on selection
        $referrer = $request->binary;
        $newmember = Member::find($memberId);
        
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
                    
                
                $newmember->update();
                
                return back()->with('success', 'Member has been replaced successfully.');
            } else {
                return back()->with('error', 'Invalid binary placement.');
            }
        } 

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
    
    
    public function listDownlines(Request $request, $id)
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
    
        $user = Auth::guard('admin')->user();
        $title = "My Downlines";
    
        $mUser = Member::find($id);
        // Get all downline IDs
        $downlineDetails = $mUser->getAllDownlineDetails($mUser->id);
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
                'id'            => $id,
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
    
        return view('admin.listdownlines', compact('title', 'user', 'paginator', 'members', 'packages', 'newpackages', 'ranks', 'totalMembers', 'id'));
    }


    public function ranks(Request $request)
    {  
        $user = Auth::guard('admin')->user();

        if ($user) {
            $ranks = RankThreshold::OrderBy('id', "ASC")->get();
            $title = "Manage Ranks";
            return view('admin.ranks', compact('title', 'user', 'ranks'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }
    

    public function addrank($id=null)
    {  
        $user = Auth::guard('admin')->user();


        if ($user) {
            
            if($id != NULL){
                $title = "Update Rank";
                $id = Crypt::decrypt($id);
                $data = RankThreshold::Find($id);
                return view('admin.newrank', compact('title', 'user', 'data'));
            }else{
                $title = "Add Rank"; 
                return view('admin.newrank', compact('title', 'user'));
            }
            
            
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function createrank(Request $request){

        $request->validate([
            'name'      => 'required|string|max:255',
            'left'      => 'nullable|integer',
            'right'     => 'nullable|integer',
            'pv'        => 'nullable|integer',
            'from'      => 'nullable|integer',
            'to'        => 'nullable|integer',
        ]);

        $rank = new RankThreshold();
        $rank->rank                     = $request->name;
        $rank->left_points_threshold    = $request->left;
        $rank->right_points_threshold   = $request->right;
        $rank->pv_requirement           = $request->pv;
        $rank->level_from               = $request->from;
        $rank->level_to                 = $request->to;

        $rank->save();

        return redirect('admin/ranks')->with('success', 'Rank has been created successfully.');
    }

    public function deleterank($id){
        $id = Crypt::decrypt($id);
        $data = RankThreshold::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Rank has been deleted successfully.');
        }
    }


    public function updaterank(Request $request){
        $request->validate([
            'id'        => 'required|integer',
            'name'      => 'required|string|max:255',
            'left'      => 'nullable|integer',
            'right'     => 'nullable|integer',
            'pv'        => 'nullable|integer',
            'from'      => 'nullable|integer',
            'to'        => 'nullable|integer',
        ]);


        $rank = RankThreshold::where('id', $request->id)->first();

        if($rank && !empty($rank )){
            $rank->rank                     = $request->name;
            $rank->left_points_threshold    = $request->left;
            $rank->right_points_threshold   = $request->right;
            $rank->pv_requirement           = $request->pv;
            $rank->level_from               = $request->from;
            $rank->level_to                 = $request->to;

            $rank->update();
            return redirect('admin/ranks')->with('success', 'Rank has been updated successfully.');
        }

        return back()->with('error', 'Error updating product.');
    }
    
    public function ranklevel(Request $request)
    {  
        $user = Auth::guard('admin')->user();

        if ($user) {
            $ranks = RankLevel::OrderBy('id', "ASC")->get();
            $title = "Manage Rank level";
            return view('admin.ranklevels', compact('title', 'user', 'ranks'));
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }
    
    public function addranklevel($id=null)
    {  
        $user = Auth::guard('admin')->user();


        if ($user) {
            
            if($id != NULL){
                $title = "Update Rank Level";
                $id = Crypt::decrypt($id);
                $data = RankLevel::Find($id);
                return view('admin.newranklevel', compact('title', 'user', 'data'));
            }else{
                $title = "Add Rank Level"; 
                return view('admin.newranklevel', compact('title', 'user'));
            }
            
            
        }
        

        return redirect('admin')->with('error', 'Unauthorized access.');
    }

    public function createranklevel(Request $request){

        $request->validate([
            'ranklevel' => 'required|integer|max:255',
            'pv'        => 'required|numeric',
        ]);

        $rank = new RankLevel();
        $rank->ranklevel    = $request->ranklevel;
        $rank->pv           = $request->pv;

        $rank->save();

        return redirect('admin/ranklevel')->with('success', 'Rank level has been created successfully.');
    }

    
    public function updateranklevel(Request $request){
        $request->validate([
            'id'        => 'required|integer',
            'ranklevel' => 'required|integer|max:255',
            'pv'        => 'required|numeric',
        ]);


        $rank = RankLevel::where('id', $request->id)->first();

        if($rank && !empty($rank )){
            $rank->ranklevel    = $request->ranklevel;
            $rank->pv           = $request->pv;

            $rank->update();
            return redirect('admin/ranklevel')->with('success', 'Rank level has been updated successfully.');
        }

        return back()->with('error', 'Error updating product.');
    }
    
    public function deleteranklevel($id){
        $id = Crypt::decrypt($id);
        $data = RankLevel::where('id',$id)->delete();
        
        if($data){
            return back()->with('success', 'Rank Level has been deleted successfully.');
        }
    }
    

    public function cms(){
        $title = "Content Management";
        $user = Auth::guard()->user();
        $cms = Page::all();

        return view('admin.cms', compact('user','title', 'cms'));
    }
    
    public function newcms($id=null){
        $title = "Content Management";
        $user = Auth::guard()->user();
        
        if($id != null){
            $id = Crypt::decrypt($id);
            $content = Page::where('id', $id)->first();
            return view('admin.managecms', compact('user','title', 'content'));
        }

        return view('admin.managecms', compact('user','title'));
    }

    public function createcms(Request $request, $id=null){
        $user = Auth::guard()->user();

        if($request->has('_token') && isset($request->update) && $request->update == 1){
                
            $request->validate([
                'name'    => ['required', 'string'],
                'content' => ['nullable', 'string'],
                'image'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'], // Validate that the file is an image
            ]);

            $content = new Page();

            if($request->hasFile('image')){
                //save page image
                $rand = Carbon::now()->format('YmdHis');
                $filename = $rand.'.'.$request->image->extension();
                $request->image->move('front/assets/images', $filename);
                $content->image = $filename;
            }

            $content->name       = $request->name;
            $content->content    = $request->content;
            $content->save();

            return redirect('admin/cms')->with('success', 'CMS has been created successfully.');
                
        }

    }
    
    
    public function updatecms(Request $request, $id=null){
        $user = Auth::guard()->user();

        if($id != null){
            $id = Crypt::decrypt($id);
            $title = "Update CMS";
            $content = Page::where('id', $id)->first();

            return view('admin.managecms', compact('user','title', 'content'));
        }

        if($request->has('_token') && isset($request->update) && $request->update == 1){
                
            request()->validate([
                'id'               => ['required', 'integer'],
                'name'             => ['required', 'string'],
                'content'          => ['nullable', 'string'],
                'image'            => ['nullable'],
            ]);

            $content = Page::where('id', $request->id)->first();

            if($request->hasFile('image')){
                //save page image
                $rand = Carbon::now()->format('YmdHis');
                $filename = $rand.'.'.$request->image->extension();
                $request->image->move('front/assets/images', $filename);
                $content->image = $filename;
            }

            $content->name       = $request->name;
            $content->content    = $request->content;
            $content->update();

            return back()->with('success', 'CMS has been updated successfully.');
                
        }

    }
    
    public function sendemails(){
        $title = "Send Bulk Email";
        $user = Auth::guard()->user();
        
        
        $members = Member::all();
        return view('admin.sendemail', compact('user','title', 'members'));
       
    }
    
    public function bulkemail(Request $request){
        $request->validate([
            'member.*'  => 'required|string|max:16',
            'subject'   => 'required|string',
            'message'   => 'required|string',
        ]);
        
        $members = []; 
        
        foreach ($request->member as $member) {
            if ($member == "all") {
                // Get all members' IDs
                $memberall = Member::select('id')->get()->pluck('id')->toArray();
                $members = array_merge($members, $memberall);
            } elseif ($member == "active") {
                // Get active members' IDs (orders within the last 30 days)
                $oneMonthAgo = now()->subDays(30);
                $memberactive = Member::select('members.id')
                                ->join('orders', 'orders.member_id', '=', 'members.id')
                                ->where('orders.created_at', '>=', $oneMonthAgo)
                                ->get()
                                ->pluck('id')
                                ->toArray();
                $members = array_merge($members, $memberactive);
            } elseif ($member == "inactive") {
                // Get inactive members' IDs (orders older than 30 days)
                $oneMonthAgo = now()->subDays(30);
                $memberinactive = Member::select('members.id')
                                ->join('orders', 'orders.member_id', '=', 'members.id')
                                ->where('orders.created_at', '<', $oneMonthAgo)
                                ->get()
                                ->pluck('id')
                                ->toArray();
                $members = array_merge($members, $memberinactive);
            } else {
                // Get specific member by ID
                $specificMember = Member::select('id')->where('id', $member)->first();
                if ($specificMember) {
                    $members[] = $specificMember->id;
                }
            }
        }
        
        $members = array_unique($members);
        
        $memberz = Member::whereIn('id', $members)
                         ->orderBy('id', 'DESC')
                         ->get();
        
        //dd($memberz);
        
        if(isset($memberz) && count($memberz) > 0){
            
            $subject = $request->subject;
        
            // Create the HTML message
            $message = "
                <html>
                <head>
                    <title>Biocaremaxplus</title>
                    <meta charset='UTF-8'>
                </head>
                <body>
                    <h1>Hello,</h1>
                    <p>.'$request->message'.</p>
                    <p>Best Regards,<br>BCM Support Team</p>
                </body>
                </html>";
    
            
            // Set content-type header for HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            // Additional headers
            $headers .= 'From: Biocaremaxplus <info@biocaremaxplus.com>' . "\r\n";
            
            
            foreach($memberz as $memb){
                $to = $memb->email;
                
                // Send the email
                mail($to, $subject, $message, $headers);
            }
        }
        
        return back()->with('success', 'Email was sent successfully.');

    }


    public static function getcategory($id){
        $data = Category::where('id', $id)->first();
        return $data;
    }

    public static function getstore($id){
        $data = Store::where('id', $id)->first();
        return $data;
    }

    public static function getCountry($id){
        $data = Country::where('id', $id)->first();
        return $data;
    }

    public static function getSettings(){
        $data = Setting::all()->keyBy('id');
        return $data;
    }

    public static function getCategories(){
        $data = Category::all();
        return $data;
    }
    
    

    public function RandomString($length, $charset='123456789'){
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count-1)];
        }
        return $str;
    }
}
