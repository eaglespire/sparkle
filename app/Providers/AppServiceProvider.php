<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Settings;
use App\Models\Blog;
use App\Models\Logo;
use App\Models\Social;
use App\Models\Faq;
use App\Models\Category;
use App\Models\Page;
use App\Models\Design;
use App\Models\About;
use App\Models\Review;
use App\Models\User;
use App\Models\Services;
use App\Models\Brands;
use App\Models\Transactions;
use App\Models\Ticket;
use App\Models\Contact;
use App\Models\Exttransfer;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        View::composer('*', function($view){
            if (Auth::guard('admin')->check()) {
                $view->with('admin', Admin::find(Auth::guard('admin')->user()->id));
                $view->with('pending_payout', Transactions::wheremode(1)->wheretype(3)->count());
                $view->with('unread', Contact::whereseen(0)->count());
                $view->with('pticket', Ticket::where('status', 0)->get());
            }
            if (Auth::guard('user')->check()) {
                $view->with('user', User::find(Auth::guard('user')->user()->id));
            }
            $view->with('set', Settings::first());
            if(url()->current()!=route('ipn.flutter')){
                //sub_check();
            }
        });
        $data['set']=Settings::first();
        $data['blog']=Blog::whereStatus(1)->get();
        $data['logo']=Logo::first();
        $data['social']=Social::all();
        $data['faq']=Faq::all();
        $data['cat']=Category::all();
        $data['pages']=Page::whereStatus(1)->get();
        $data['ui']=Design::first();
        $data['about']=About::first();
        $data['trending'] = Blog::whereStatus(1)->orderBy('views', 'DESC')->limit(5)->get();
        $data['posts'] = Blog::whereStatus(1)->orderBy('views', 'DESC')->limit(5)->get();
        $data['review'] = Review::whereStatus(1)->get();
        $data['item'] = Services::all();
        $data['item4'] = Services::whereId(4)->first();
        $data['brand'] = Brands::whereStatus(1)->get();
        $data['xfaq']=Faq::first();
        view::share($data);
    }
}
