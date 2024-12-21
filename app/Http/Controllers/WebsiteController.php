<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Pages;
use App\Models\Store;
use App\Models\Setting;
use App\Models\Page;
use App\Models\Country;
use App\Models\Currency;

class WebsiteController extends Controller
{
    public function homepage()
    {  

        $title = "HomePage";
        $banners = Banner::all();
        $categories = Category::orderBy('id', 'ASC')->take(10)->get();
        $discountProducts = Product::whereJsonContains('store', '3')
                                    ->whereNotNull('image')
                                    ->orderBy('id', 'DESC')
                                    ->take(10)
                                    ->get();

        $featuredProducts = Product::inRandomOrder()->take(12)->get();
        
       
        return view('front.home', compact('title', 'banners', 'discountProducts', 'featuredProducts', 'categories'));
        
        
    }


    public function getConversionRate(Request $request)
    {
        
        $packageId = $request->input('packageId');
        $conversion = html_entity_decode("&#8358;");

        return response()->json(['conversion' => $conversion, 'price' => $request->packageId]);
        
    }

    public function getConversionRateold(Request $request)
    {
        $countryId = $request->input('country_id');
        $packageId = $request->input('package_id');
        $currency = Currency::whereJsonContains('country', $countryId)->first();
        
        

        if ($currency) {
            $price =  ($packageId * $currency->conversion);
            $symbol = html_entity_decode($currency->symbol);
            $conversion = $symbol .' '. number_format($price);

            return response()->json(['conversion' => $conversion, 'price' => $price]);
        } else {
            return response()->json(['conversion' => null], 404);
        }
    }
    
    public function aboutus(){
        $title = "About Us";
        $page = Page::find(1);


        return view('front.aboutus', compact('title', 'page'));
    }
    
    public function announcements(){
        $title = "Announcements";
        $pages = Page::where('id', '!=', '1')->OrderBy('id', "DESC")->get();


        return view('front.announcements', compact('title', 'pages'));
    }
    
    public function contactus(){
        $title = "Contact Us";
        $page = Page::find(1);
        $settings = $this->getSettings();

        return view('front.contactus', compact('title', 'settings'));
    }
    
    public function sendmessage(Request $request){
        // Validate form inputs
        $validatedData = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email',
            'phone'     => 'required',
            'subject'   => 'required|string|max:255',
            'message'   => 'required|string|max:555',
        ]);
        
        
        // Send email to the sender confirming the message has been received
        $toSender = $validatedData['email'];
        $subjectToSender = 'Your message has been received';
        $messageToSender = "Dear {$validatedData['name']},\n\nThank you for contacting us. We have received your message and will get back to you shortly.\n\nBest regards,\nBiocaremaxplus Team";
        $headersToSender = 'From: info@biocaremaxplus.com';
    
        mail($toSender, $subjectToSender, $messageToSender, $headersToSender);
    
        // Send the content of the form to the recipient
        $toRecipient = 'info@biocaremaxplus.com';
        $subjectToRecipient = 'New Contact Us Form Submission';
        $messageToRecipient = "You have received a new message from {$validatedData['name']}.\n\n".
                              "Email: {$validatedData['email']}\n".
                              "Phone: {$validatedData['phone']}\n".
                              "Subject: {$validatedData['subject']}\n\n".
                              "Message: {$validatedData['message']}\n\n".
                              "Please respond accordingly.";
        $headersToRecipient = 'From: ' . $validatedData['email'];
    
        mail($toRecipient, $subjectToRecipient, $messageToRecipient, $headersToRecipient);
    
        // Redirect with success message
        return redirect()->back()->with('success', 'Your message has been sent successfully.');


    }
    
    public static function getSettings(){
        $data = Setting::all()->keyBy('id');
        return $data;
    }
    
}
