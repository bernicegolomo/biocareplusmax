<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


Route::get('/run-monthly-earnings', [MemberController::class, 'settleMonthlyTeamBonus']);
Route::get('/run-team-bonus', [MemberController::class, 'processCronTeamBonus']);

Route::get('/', [WebsiteController::class, 'homepage'])->name('homepage');
Route::get('/get-conversion-rate', [WebsiteController::class, 'getConversionRate']);

Route::get('/aboutus', [WebsiteController::class, 'aboutus']); 
Route::get('/announcements', [WebsiteController::class, 'announcements']);
Route::get('/contactus', [WebsiteController::class, 'contactus']);
Route::post('/sendmessage', [WebsiteController::class, 'sendmessage']);

Route::get('/allproducts/{id}', [MemberController::class, 'allproducts'])->name('allproducts');


Route::get('/selectpackage', [MemberController::class, 'packages'])->name('packages');
Route::get('/register/{id}', [MemberController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [MemberController::class, 'register']);
Route::get('/login', [MemberController::class, 'showLoginForm'])->name('login');
Route::post('/login', [MemberController::class, 'login']);
Route::get('/logout', [MemberController::class, 'logout'])->name('logout');
Route::get('/forgotpassword', [MemberController::class, 'forgotpassword'])->name('forgotpassword');
Route::post('/passwordreset', [MemberController::class, 'passwordreset'])->name('passwordreset');

Route::get('password/reset', [MemberController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [MemberController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [MemberController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [MemberController::class, 'resetPassword'])->name('password.update');

Route::get('/getdownlines', [MemberController::class, 'getDownlineTree'])->name('getDownlineTree');
Route::get('/getdownlines/{id}', [MemberController::class, 'getDownlineTree'])->name('getDownlineTree');

Route::middleware(['auth:web'])->group(function () {
    Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('dashboard');
    Route::post('/addtocart', [MemberController::class, 'addToCart'])->name('addToCart');
    Route::post('/removecartitem', [MemberController::class, 'removeCartItem'])->name('removeCartItem');
    
    Route::post('/updatecart', [MemberController::class, 'updateCart'])->name('updateCart');
    Route::get('/getcart', [MemberController::class, 'getCart'])->name('getCart');
    Route::get('/cart', [MemberController::class, 'cartIndex'])->name('cartIndex');
    
    Route::get('/mydownlines', [MemberController::class, 'mydownlines'])->name('mydownlines');
    Route::any('/listdownlines', [MemberController::class, 'listdownlines'])->name('listdownlines');
    Route::get('/mydownlines/{id}', [MemberController::class, 'mydownlines'])->name('mydownlines');
    Route::get('/newdownline', [MemberController::class, 'newdownline'])->name('newdownline');
    Route::get('/registerdownline/{id}', [MemberController::class, 'registerdownline']);
    Route::post('/registerdownline', [MemberController::class, 'memberregister']);
    Route::get('/myprofile', [MemberController::class, 'myprofile']);
    Route::post('/updatemyprofile', [MemberController::class, 'updatemyprofile']);


    Route::post('/removefromcart', [MemberController::class, 'removeFromCart'])->name('removeFromCart');
    Route::post('/checkout', [MemberController::class, 'checkout'])->name('checkout');
    Route::any('/wallets', [MemberController::class, 'wallets'])->name('wallets');
    Route::get('/myorders', [MemberController::class, 'myorders'])->name('myorders');
    Route::get('/mypackages', [MemberController::class, 'mypackages'])->name('mypackages');
    Route::post('/upgradepackage', [MemberController::class, 'upgradepackage'])->name('upgradepackage');
    Route::get('/mytransactions', [MemberController::class, 'mytransactions'])->name('mytransactions');
    Route::get('/stockistBackOffice', [MemberController::class, 'stockistBackOffice'])->name('stockistBackOffice');
    Route::get('/stockistprocessorder/{id}', [MemberController::class, 'stockistprocessorder'])->name('stockistprocessorder');
    Route::get('/stockistconfirmorder/{id}', [MemberController::class, 'stockistconfirmorder'])->name('stockistconfirmorder');
    
    Route::get('/transferVoucher', [MemberController::class, 'transferVoucher'])->name('transferVoucher');
    Route::post('/transfermembervoucher', [MemberController::class, 'transfermembervoucher'])->name('transfermembervoucher');
    
    
    Route::get('/stores', [MemberController::class, 'stores'])->name('stores');
    Route::get('/store/{id}', [MemberController::class, 'store'])->name('store');
    Route::get('/store/{id}/{catid}', [MemberController::class, 'store'])->name('store');
    Route::post('/store', [MemberController::class, 'store'])->name('store');
    
    Route::any('/withdrawals', [MemberController::class, 'withdrawals'])->name('withdrawals');
    Route::get('/requestwithdrawal', [MemberController::class, 'requestwithdrawal'])->name('requestwithdrawal');
    Route::post('/processwithdrawal', [MemberController::class, 'processwithdrawal'])->name('processwithdrawal');
    
});




Route::get('/admin', function () {
    return view('admin.login');
})->name('admin.login');

Route::post('/admin', [AdminController::class, 'login']);
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth:admin'])->group(function () {
   
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/admin/create', [AdminController::class, 'createAdmin']);
    
    Route::any('/admin/admins', [AdminController::class, 'admins'])->name('admins');
    Route::get('/newadmin', [AdminController::class, 'newadmin'])->name('newadmin');
    Route::get('/editadmin/{id}', [AdminController::class, 'newadmin'])->name('newadmin');
    Route::get('/deleteadmin/{id}', [AdminController::class, 'deleteadmin'])->name('deleteadmin');
    Route::post('/updateadminprofile', [AdminController::class, 'updateadmin'])->name('updateadmin');
    

    Route::any('/admin/members', [AdminController::class, 'members'])->name('members');
    Route::get('/viewmember/{id}', [AdminController::class, 'viewmember'])->name('viewmember');
    Route::get('/deletemember/{id}', [AdminController::class, 'deletemember'])->name('deletemember');
    Route::post('/updatememberprofile', [AdminController::class, 'updatememberprofile'])->name('updatememberprofile');
    Route::get('/admin/membertree/{id}', [AdminController::class, 'membertree'])->name('membertree');
    Route::post('/updatebinaryplacement', [AdminController::class, 'updatebinaryplacement'])->name('updatebinaryplacement');
    Route::any('/admin/listdownlines/{id}', [AdminController::class, 'listdownlines'])->name('listdownlines');
    
    Route::get('/admin/activemembers', [AdminController::class, 'activemembers'])->name('activemembers');
    Route::get('/admin/inactivemembers', [AdminController::class, 'inactivemembers'])->name('inactivemembers');
    
    Route::any('/admin/products', [AdminController::class, 'products'])->name('products');
    Route::get('/admin/newproduct', [AdminController::class, 'newproduct'])->name('newproduct');
    Route::get('/admin/newproduct/{id}', [AdminController::class, 'newproduct'])->name('newproduct');
    Route::post('/addproduct', [AdminController::class, 'createproduct'])->name('createproduct');
    Route::get('/deleteproduct/{id}', [AdminController::class, 'deleteproduct'])->name('deleteproduct');
    Route::post('/updateproduct', [AdminController::class, 'updateproduct'])->name('updateproduct');
    
    Route::any('/admin/categories', [AdminController::class, 'categories'])->name('categories');
    Route::get('/admin/newcategory', [AdminController::class, 'newcategory'])->name('newcategory');
    Route::get('/admin/newcategory/{id}', [AdminController::class, 'newcategory'])->name('newcategory');
    Route::post('/addcategory', [AdminController::class, 'createcategory'])->name('createcategory');
    Route::get('/deletecategory/{id}', [AdminController::class, 'deletecategory'])->name('deletecategory');
    Route::post('/updatecategory', [AdminController::class, 'updatecategory'])->name('updatecategory');
    
    
    Route::any('/admin/stores', [AdminController::class, 'stores'])->name('stores');
    Route::get('/admin/newstore', [AdminController::class, 'newstore'])->name('newstore');
    Route::get('/admin/newstore/{id}', [AdminController::class, 'newstore'])->name('newstore');
    Route::get('/admin/activatestore/{id}', [AdminController::class, 'activatestore'])->name('activatestore');
    Route::get('/admin/deactivatestore/{id}', [AdminController::class, 'deactivatestore'])->name('deactivatestore');
    Route::post('/createstore', [AdminController::class, 'createstore'])->name('createstore');
    Route::post('/updatestore', [AdminController::class, 'updatestore'])->name('updatestore');
    Route::get('/deletestore/{id}', [AdminController::class, 'deletestore'])->name('deletestore');
    
    Route::any('/admin/stockist', [AdminController::class, 'pickup'])->name('pickup');
    Route::any('/admin/pickup', [AdminController::class, 'pickup'])->name('pickup');
    Route::get('/admin/newpickup', [AdminController::class, 'newpickup'])->name('newpickup');
    Route::get('/admin/activatepickup/{id}', [AdminController::class, 'activatepickup'])->name('activatepickup');
    Route::get('/admin/deactivatepickup/{id}', [AdminController::class, 'deactivatepickup'])->name('deactivatepickup');
    Route::post('/createpickup', [AdminController::class, 'createpickup'])->name('createpickup');
    Route::get('/deletepickup/{id}', [AdminController::class, 'deletepickup'])->name('deletepickup');
    
    Route::any('/admin/stockistpackages', [AdminController::class, 'stockistpackages'])->name('stockistpackages');
    Route::get('/admin/newstokistpackage', [AdminController::class, 'newstokistpackage'])->name('newstokistpackage');
    Route::get('/admin/newstokistpackage/{id}', [AdminController::class, 'newstokistpackage'])->name('newstokistpackage');
    Route::post('/addstockistpackage', [AdminController::class, 'createstockistpackage'])->name('createstockistpackage');
    Route::post('/updatestockistpackage', [AdminController::class, 'updatestockistpackage'])->name('updatestockistpackage');
    Route::get('/deletestockistpackage/{id}', [AdminController::class, 'deletestockistpackage'])->name('deletestockistpackage');
    

    

    Route::any('/admin/packages', [AdminController::class, 'packages'])->name('packages');
    Route::get('/admin/newpackage', [AdminController::class, 'newpackage'])->name('newpackage');
    Route::get('/admin/newpackage/{id}', [AdminController::class, 'newpackage'])->name('newpackage');
    Route::post('/addpackage', [AdminController::class, 'createpackage'])->name('createpackage');
    Route::post('/updatepackage', [AdminController::class, 'updatepackage'])->name('updatepackage');
    Route::get('/deletepackage/{id}', [AdminController::class, 'deletepackage'])->name('deletepackage');
    
    Route::any('/admin/tokens', [AdminController::class, 'tokens'])->name('tokens');
    Route::get('/admin/newtoken', [AdminController::class, 'newtoken'])->name('newtoken');
    Route::get('/admin/newtoken/{id}', [AdminController::class, 'newtoken'])->name('newtoken');
    Route::post('/generatetoken', [AdminController::class, 'generatetoken'])->name('generatetoken');
    Route::get('/deletetoken/{id}', [AdminController::class, 'deletetoken'])->name('deletetoken');
    
    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/processorder/{id}', [AdminController::class, 'processorder'])->name('processorder');
    Route::get('/deleteorder/{id}', [AdminController::class, 'deleteorder'])->name('deleteorder');
    
    Route::any('/admin/earnings', [AdminController::class, 'earnings'])->name('earnings');
    Route::get('/deletewallet/{id}/{transaction_type}', [AdminController::class, 'deleteWallet'])->name('deletewallet');
    Route::post('/delete-multiple-wallets', [AdminController::class, 'deleteMultipleWallets'])->name('deleteMultipleWallets');
    
    
    Route::get('/admin/creditmember', [AdminController::class, 'creditmember'])->name('creditmember');
    Route::get('/admin/debitmember', [AdminController::class, 'debitmember'])->name('debitmember');
    Route::post('/addearnings', [AdminController::class, 'addearnings'])->name('addearnings');
    Route::post('/debitearnings', [AdminController::class, 'debitearnings'])->name('debitearnings');
    
    Route::any('/admin/withdrawals', [AdminController::class, 'withdrawals'])->name('withdrawals');
    Route::get('/deletewithdrawal/{id}', [AdminController::class, 'deletewithdrawal'])->name('deletewithdrawal');
    Route::post('/processrequest', [AdminController::class, 'processrequest'])->name('processrequest');
    
    Route::any('/currency', [AdminController::class, 'currency'])->name('currency');
    Route::get('/admin/newcurrency', [AdminController::class, 'newcurrency'])->name('newcurrency');
    Route::post('/addcurrency', [AdminController::class, 'createcurrency'])->name('createcurrency');
    Route::get('/deletecurrency/{id}', [AdminController::class, 'deletecurrency'])->name('deletecurrency');
    

    Route::any('/admin/banners', [AdminController::class, 'banners'])->name('banners');
    Route::get('/admin/newbanner', [AdminController::class, 'newbanner'])->name('newbanner');
    Route::post('/createbanner', [AdminController::class, 'createbanner'])->name('createbanner');
    Route::get('/deletebanner/{id}', [AdminController::class, 'deletebanner'])->name('deletebanner');
    
    Route::get('/admin/directreferralbonus',[AdminController::class, 'directreferralbonus'])->name('directreferralbonus');
    Route::get('/deletedirectreferral/{id}', [AdminController::class, 'deletedirectreferral'])->name('deletedirectreferral');
    Route::post('/addDirectReferralBonus',[AdminController::class, 'addDirectReferralBonus'])->name('addDirectReferralBonus');
    
    Route::get('/admin/ranks',[AdminController::class, 'ranks'])->name('ranks');
    Route::get('/deleterank/{id}', [AdminController::class, 'deleterank'])->name('deleterank');
    Route::get('/admin/addrank',[AdminController::class, 'addrank'])->name('addrank');
    Route::get('/admin/addrank/{id}',[AdminController::class, 'addrank'])->name('addrank');
    Route::post('/createrank',[AdminController::class, 'createrank'])->name('createrank');
    Route::post('/updaterank',[AdminController::class, 'updaterank'])->name('updaterank');
    
    Route::get('/admin/ranklevel',[AdminController::class, 'ranklevel'])->name('ranklevel');
    Route::get('/deleteranklevel/{id}', [AdminController::class, 'deleteranklevel'])->name('deleteranklevel');
    Route::get('/admin/addranklevel',[AdminController::class, 'addranklevel'])->name('addranklevel');
    Route::get('/admin/addranklevel/{id}',[AdminController::class, 'addranklevel'])->name('addranklevel');
    Route::post('/createranklevel',[AdminController::class, 'createranklevel'])->name('createranklevel');
    Route::post('/updateranklevel',[AdminController::class, 'updateranklevel'])->name('updateranklevel');
    
    
    Route::get('/admin/settings',[AdminController::class, 'settings'])->name('settings');
    Route::post('/updatesettings',[AdminController::class, 'updatesettings'])->name('updatesettings');
      
    Route::get('/admin/banks', [AdminController::class, 'banks'])->name('banks');
    Route::get('/admin/newbank', [AdminController::class, 'newbank'])->name('newbanner');
    Route::post('/createbank', [AdminController::class, 'createbank'])->name('createbank');
    Route::get('/deletebank/{id}', [AdminController::class, 'deletebank'])->name('deletebank');
    

    Route::get('/admin/cms',[AdminController::class, 'cms'])->name('cms');  
    Route::get('/addcms',[AdminController::class, 'newcms'])->name('newcms');
    Route::post('/createcms',[AdminController::class, 'createcms'])->name('createcms');
    Route::get('/deletecms/{id}',[AdminController::class, 'deletecms'])->name('deletecms');
    Route::get('/editcms/{id}',[AdminController::class, 'newcms'])->name('newcms');
    Route::post('/updatecms',[AdminController::class, 'updatecms'])->name('updatecms');
    
    Route::get('/sendemails',[AdminController::class, 'sendemails'])->name('sendemails');
    Route::post('/bulkemail',[AdminController::class, 'bulkemail'])->name('bulkemail');

    
});


