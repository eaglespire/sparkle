<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\Blog;
use App\Models\Faq;
use App\Models\Faqcategory;
use App\Models\Category;
use App\Models\Page;
use App\Models\Review;
use App\Models\Contact;
use Illuminate\Http\Request;

class FrontendController extends Controller
{

    public function __construct()
    {
    }

    public function index()
    {
        $set = Settings::first();
        $data['title'] = $set->title;
        return view('front.index', $data);
    }


    public function about()
    {
        $data['title'] = "About Us";
        $data['review'] = Review::whereStatus(1)->get();
        return view('front.about', $data);
    }

    public function faq()
    {
        $data['title'] = "Knowledge base";
        return view('front.faq', $data);
    }

    public function terms()
    {
        $data['title'] = "Terms & conditions";
        return view('front.terms', $data);
    }

    public function privacy()
    {
        $data['title'] = "Privacy policy";
        return view('front.privacy', $data);
    }


    public function contact()
    {
        $data['title'] = "Contact Us";
        return view('front.contact', $data);
    }


    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'message' => 'required'
        ]);
        $sav['full_name'] = $request->name;
        $sav['email'] = $request->email;
        $sav['mobile'] = $request->mobile;
        $sav['message'] = $request->message;
        $sav['seen'] = 0;
        Contact::create($sav);
        return back()->with('success', ' Message was successfully sent!');
    }


    public function blog()
    {
        $data['title'] = "News & Articles";
        $data['posts'] = Blog::orderby('created_at', 'desc')->paginate(9);
        return view('front.blog', $data);
    }

    public function pricing()
    {
        $data['title'] = "Pricing";
        return view('front.pricing', $data);
    }

    public function developers()
    {
        $data['title'] = "Documentation";
        return view('front.developers', $data);
    }

    public function article($id)
    {
        $post = $data['post'] = Blog::find($id);
        $xcat = $data['xcat'] = Category::find($post->cat_id);
        $post->views += 1;
        $post->save();
        $data['title'] = $data['post']->title;
        return view('front.single', $data);
    }

    public function answer($id, $slug)
    {
        $data = Faq::find($id);
        $data['title'] = $data->question;
        $data['faq'] = $data;
        return view('front.faq-view', $data);
    }

    public function all($id, $slug)
    {
        $data['title'] = Faqcategory::whereid($id)->first()->name;
        $data['faq'] = Faq::wherecat_id($id)->get();
        return view('front.faq-all', $data);
    }
    public function faqSubmit(Request $request)
    {
        $val = Faq::where('question', 'LIKE', '%' . $request->search . '%')->orwhere('answer', 'LIKE', '%' . $request->search . '%')->get();
        if (count($val) > 0) {
            $data['title'] = $request->search;
            $data['faq'] = $val;
            return view('front.faq-all', $data);
        } else {
            return back()->with('alert', 'Sorry, nothing found');
        }
    }

    public function category($id)
    {
        $cat = Category::find($id);
        $data['title'] = $cat->categories;
        $data['posts'] = Blog::where('cat_id', $id)->latest()->paginate(3);
        return view('front.cat', $data);
    }


    public function page($id)
    {
        $page = $data['page'] = Page::find($id);
        $data['title'] = $page->title;
        return view('front.pages', $data);
    }
}
