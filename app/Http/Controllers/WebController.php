<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logo;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Social;
use App\Models\About;
use App\Models\Faq;
use App\Models\Faqcategory;
use App\Models\Page;
use App\Models\Review;
use App\Models\Services;
use App\Models\Plugins;
use App\Models\Brands;
use App\Models\Design;
use Image;





class WebController extends Controller
{

    //Social
    public function sociallinks()
    {
        $data['title'] = 'Social links';
        $data['links'] = Social::latest()->get();
        return view('admin.web-control.social-links', $data);
    }
    public function UpdateSocial(Request $request)
    {
        $mac = Social::findOrFail($request->id);
        $mac['value'] = $request->link;
        $res = $mac->save();
        if ($res) {
            return back()->with('success', ' Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating link');
        }
    }
    //

    //About
    public function aboutus()
    {
        $data['title'] = 'About us';
        $data['value'] = About::first();
        return view('admin.web-control.about-us', $data);
    }
    public function UpdateAbout(Request $request)
    {
        $mac = About::findOrFail(1);
        $mac['about'] = Purifier::clean($request->details);
        $res = $mac->save();
        if ($res) {
            return back()->with('success', ' Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating link');
        }
    }
    //

    //Faq
    public function faq()
    {
        $data['title'] = 'Knowledge base';
        $data['fcat'] = Faqcategory::orderby('id', 'desc')->get();
        $data['faq'] = Faq::orderby('id', 'desc')->get();
        return view('admin.web-control.faq', $data);
    }

    public function DestroyFaq($id)
    {
        $data = Faq::findOrFail($id);
        $res =  $data->delete();
        if ($res) {
            return back()->with('success', 'Faq was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting Faq');
        }
    }

    public function delfaqcategory($id)
    {
        $data = Faqcategory::findOrFail($id);
        $check = Faq::wherecat_id($id)->count();
        if ($check > 0) {
            return back()->with('alert', 'Problem With Deleting Category, it already used for an existing faq');
        } else {
            $data->delete();
            return back()->with('success', 'Category was Successfully deleted!');
        }
    }

    public function CreateFaqcategory(Request $request)
    {
        $macCount = Faqcategory::where('name', $request->name)->count();
        if ($macCount > 0) {
            return back()->with('alert', 'This one Already Exist');
        } else {
            $data['name'] = $request->name;
            $data['slug'] = str_slug($request->name);
            $res = Faqcategory::create($data);
            if ($res) {
                return back()->with('success', 'Saved Successfully!');
            } else {
                return back()->with('alert', 'Problem With Adding New Category');
            }
        }
    }

    public function UpdateFaqcategory(Request $request)
    {
        $mac = Faqcategory::findOrFail($request->id);
        $mac['name'] = $request->name;
        $mac['slug'] = str_slug($request->name);
        $res = $mac->save();
        if ($res) {
            return back()->with('success', ' Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Category');
        }
    }
    public function CreateFaq(Request $request)
    {
        $data['question'] = $request->question;
        $data['answer'] = $request->answer;
        $data['cat_id'] = $request->cat;
        $data['slug'] = str_slug($request->question);
        $res = Faq::create($data);
        if ($res) {
            return back()->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Creating New Faq');
        }
    }
    public function UpdateFaq(Request $request)
    {
        $mac = Faq::findOrFail($request->id);
        $mac['question'] = $request->question;
        $mac['answer'] = $request->answer;
        $mac['cat_id'] = $request->cat;
        $mac['slug'] = str_slug($request->question);
        $res = $mac->save();
        if ($res) {
            return back()->with('success', ' Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Faq');
        }
    }
    //End of Faq  

    //Privacy
    public function privacypolicy()
    {
        $data['title'] = 'Privacy policy';
        $data['value'] = About::first();
        return view('admin.web-control.privacy-policy', $data);
    }
    public function UpdatePrivacy(Request $request)
    {
        $mac = About::findOrFail(1);
        $mac['privacy_policy'] = Purifier::clean($request->details);
        $res = $mac->save();
        if ($res) {
            return back()->with('success', ' Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating link');
        }
    }
    //

    //Terms
    public function UpdateTerms(Request $request)
    {
        $mac = About::findOrFail(1);
        $mac['terms'] = Purifier::clean($request->details);
        $res = $mac->save();
        if ($res) {
            return back()->with('success', ' Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating link');
        }
    }
    public function terms()
    {
        $data['title'] = 'Terms & Conditions';
        $data['value'] = About::first();
        return view('admin.web-control.terms', $data);
    }
    //

    //Logos
    public function logo()
    {
        $data['title'] = 'Logo & Favicon';
        return view('admin.web-control.logo', $data);
    }

    public function dark(Request $request)
    {

        $data = Logo::find(1);
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $filename = 'logo.' . $image->extension();
            $location = public_path('asset/images/' . $filename);
            Image::make($image)->save($location);
            $path = public_path('asset');
            File::delete($path . $data->dark);
            $data['dark'] = 'images/' . $filename;
        }
        $res = $data->save();
        if ($res) {
            return back()->with('success', 'Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Logo');
        }
        return $data;
    }

    public function section1(Request $request)
    {

        $data = Design::find(1);
        if ($request->hasFile('section1')) {
            $image = $request->file('section1');
            $filename = 'section1_' . time() . '.' . $image->extension();
            $location = public_path('asset/images/' . $filename);
            Image::make($image)->save($location);
            $path = public_path('asset/images');
            File::delete($path . $data->s2_image);
            $data['s2_image'] = $filename;
        }
        $res = $data->save();
        if ($res) {
            return back()->with('success', 'Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Image');
        }
        return $data;
    }

    public function section2(Request $request)
    {

        $data = Design::find(1);
        if ($request->hasFile('section2')) {
            $image = $request->file('section2');
            $filename = 'section2_' . time() . '.' . $image->extension();
            $location = public_path('asset/images/' . $filename);
            Image::make($image)->save($location);
            $path = public_path('asset/images');
            File::delete($path . $data->s3_image);
            $data['s3_image'] = $filename;
        }
        $res = $data->save();
        if ($res) {
            return back()->with('success', 'Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Image');
        }
        return $data;
    }

    public function section3(Request $request)
    {

        $data = Design::find(1);
        if ($request->hasFile('section3')) {
            $image = $request->file('section3');
            $filename = 'section3_' . time() . '.' . $image->extension();
            $location = public_path('asset/images/' . $filename);
            Image::make($image)->save($location);
            $path = public_path('asset/images');
            File::delete($path . $data->s4_image);
            $data['s4_image'] = $filename;
        }
        $res = $data->save();
        if ($res) {
            return back()->with('success', 'Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Image');
        }
        return $data;
    }

    public function section7(Request $request)
    {

        $data = Design::find(1);
        if ($request->hasFile('section7')) {
            $image = $request->file('section7');
            $filename = 'section7_' . time() . '.' . $image->extension();
            $location = public_path('asset/images/' . $filename);
            Image::make($image)->save($location);
            $path = public_path('asset/images');
            File::delete($path . $data->s7_image);
            $data['s7_image'] = $filename;
        }
        $res = $data->save();
        if ($res) {
            return back()->with('success', 'Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Image');
        }
        return $data;
    }


    public function UpdateFavicon(Request $request)
    {

        $data = Logo::find(1);
        if ($request->hasFile('favicon')) {
            $image = $request->file('favicon');
            $filename = 'favicon.' . $image->extension();
            $location = public_path('asset/images/' . $filename);
            Image::make($image)->save($location);
            $path = public_path('asset');
            File::delete($path . $data->image_link2);
            $data['image_link2'] = 'images/' . $filename;
        }
        $res = $data->save();
        if ($res) {
            return back()->with('success', 'Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Logo');
        }
        return $data;
    }

    public function UpdatePreloader(Request $request)
    {

        $data = Logo::find(1);
        if ($request->hasFile('preloader')) {
            $image = $request->file('preloader');
            $filename = 'preloader.' . $image->extension();
            $request->file('preloader')->move(public_path('asset/images'), $filename);
            $path = public_path('asset');
            File::delete($path . $data->preloader);
            $data['preloader'] = 'images/' . $filename;
        }
        $res = $data->save();
        if ($res) {
            return back()->with('success', 'Updated Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Logo');
        }
        return $data;
    }
    //

    //Plugins
    public function Editplugin($id)
    {
        $data['title'] = 'Plugins';
        $data['val'] = Plugins::find($id);
        return view('admin.web-control.plugin-edit', $data);
    }
    public function plugin()
    {
        $data['title'] = 'plugins';
        $data['plugin'] = Plugins::latest()->get();
        return view('admin.web-control.plugin', $data);
    }
    public function unplugin($id)
    {
        $page = Plugins::find($id);
        $page->status = 0;
        $page->save();
        return back()->with('success', 'plugin has been unpublished.');
    }
    public function pplugin($id)
    {
        $page = Plugins::find($id);
        $page->status = 1;
        $page->save();
        return back()->with('success', 'plugin was successfully published.');
    }
    public function Createplugin(Request $request)
    {
        $data['title'] = $request->title;
        $data['description'] = $request->description;
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $filename = str_slug($request->title) . '.' . $image->extension();
            $request->file('file')->move(public_path('asset/plugins'), $filename);
            $data['link'] = $filename;
        }
        $res = Plugins::create($data);
        if ($res) {
            return redirect()->route('admin.plugin')->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Creating plugin');
        }
    }
    public function Destroyplugin($id)
    {
        $data = Plugins::findOrFail($id);
        $res =  $data->delete();
        if ($res) {
            return back()->with('success', 'plugin was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting plugin');
        }
    }

    public function Updateplugin(Request $request)
    {
        $data = Plugins::find($request->id);
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $filename = str_slug($request->title) . '.' . $image->extension();
            $request->file('file')->move(public_path('asset/plugins'), $filename);
            $path = public_path('asset/plugins');
            File::delete($path . $data->image);
            $data->image = $filename;
        }
        $data->title = $request->title;
        $data->description = $request->description;
        $data->save();
        return redirect()->route('admin.plugin')->with('success', 'Saved Successfully!');
    }
    //End of plugins
    //Brand
    public function EditBrand($id)
    {
        $data['title'] = 'Brands';
        $data['val'] = Brands::find($id);
        return view('admin.web-control.brand-edit', $data);
    }
    public function brand()
    {
        $data['title'] = 'Brands';
        $data['brand'] = Brands::latest()->get();
        return view('admin.web-control.brand', $data);
    }
    public function unbrand($id)
    {
        $page = Brands::find($id);
        $page->status = 0;
        $page->save();
        return back()->with('success', 'Brand has been unpublished.');
    }
    public function pbrand($id)
    {
        $page = Brands::find($id);
        $page->status = 1;
        $page->save();
        return back()->with('success', 'Brand was successfully published.');
    }
    public function CreateBrand(Request $request)
    {
        $data['title'] = $request->title;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'brand_' . time() . '.' . $image->extension();
            $location = public_path('asset/brands/' . $filename);
            Image::make($image)->save($location);
            $data['image'] = $filename;
        }
        $res = Brands::create($data);
        if ($res) {
            return redirect()->route('admin.brand')->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Creating Brand');
        }
    }
    public function DestroyBrand($id)
    {
        $data = Brands::findOrFail($id);
        $res =  $data->delete();
        if ($res) {
            return back()->with('success', 'Brand was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting Brand');
        }
    }

    public function UpdateBrand(Request $request)
    {
        $data = Brands::find($request->id);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'brand_' . time() . '.' . $image->extension();
            $location = public_path('asset/brands/' . $filename);
            Image::make($image)->save($location);
            $path = public_path('asset/brands');
            File::delete($path . $data->image);
            $data->image = $filename;
        }
        $data->title = $request->title;
        $data->save();
        return redirect()->route('admin.brand')->with('success', 'Saved Successfully!');
    }
    //

    //Review
    public function UpdateReview(Request $request)
    {
        $data = Review::find($request->id);
        $data['name'] = $request->name;
        $data['occupation'] = $request->occupation;
        $data['review'] = $request->review;
        if ($request->hasFile('update')) {
            $image = $request->file('update');
            $filename = 'update_' . time() . '.' . $image->extension();
            $location = public_path('asset/review/' . $filename);
            $path = public_path('asset/review');
            File::delete($path . $data->image_link);
            Image::make($image)->save($location);
            $data['image_link'] = $filename;
        }
        $res = $data->save();
        if ($res) {
            return redirect()->route('admin.review')->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Creating Review');
        }
    }
    public function DestroyReview($id)
    {
        $data = Review::findOrFail($id);
        $res =  $data->delete();
        if ($res) {
            return back()->with('success', 'Review was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting Review');
        }
    }
    public function CreateReview(Request $request)
    {
        $data['name'] = $request->name;
        $data['occupation'] = $request->occupation;
        $data['review'] = $request->review;
        if ($request->hasFile('image5')) {
            $image = $request->file('image');
            $filename = 'review_' . time() . '.' . $image->extension();
            $location = public_path('asset/review/' . $filename);
            Image::make($image)->save($location);
            $data['image_link'] = $filename;
        }
        $res = Review::create($data);
        if ($res) {
            return redirect()->route('admin.review')->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Creating Review');
        }
    }
    public function review()
    {
        $data['title'] = 'Reviews';
        $data['review'] = Review::latest()->get();
        return view('admin.web-control.review', $data);
    }

    public function EditReview($id)
    {
        $data['title'] = 'Reviews';
        $data['val'] = Review::find($id);
        return view('admin.web-control.review-edit', $data);
    }
    public function unreview($id)
    {
        $page = Review::find($id);
        $page->status = 0;
        $page->save();
        return back()->with('success', 'Review has been unpublished.');
    }
    public function preview($id)
    {
        $page = Review::find($id);
        $page->status = 1;
        $page->save();
        return back()->with('success', 'Review was successfully published.');
    }

    //

    //Service
    public function services()
    {
        $data['title'] = 'Services';
        $data['service'] = Services::latest()->get();
        return view('admin.web-control.service', $data);
    }
    public function DestroyService($id)
    {
        $data = Services::findOrFail($id);
        $res =  $data->delete();
        if ($res) {
            return back()->with('success', 'Service was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting Service');
        }
    }
    public function EditService($id)
    {
        $data['title'] = 'Service';
        $data['val'] = Services::find($id);
        return view('admin.web-control.service-edit', $data);
    }
    public function CreateService(Request $request)
    {
        $data['title'] = $request->title;
        $data['details'] = $request->details;
        $res = Services::create($data);
        if ($res) {
            return redirect()->route('admin.service')->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Creating New Service');
        }
    }
    public function UpdateService(Request $request)
    {
        $data = Services::find($request->id);
        $data->fill($request->all())->save();
        if ($res) {
            return redirect()->route('admin.service')->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Creating Service');
        }
    }
    //

    //Webpage
    public function UpdatePage(Request $request)
    {
        $mac = Page::findOrFail($request->id);
        $mac['title'] = $request->title;
        $mac['content'] = Purifier::clean($request->content);
        $mac['slug'] = str_slug($request->title);
        $res = $mac->save();
        if ($res) {
            return redirect()->route('admin.page')->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Page');
        }
    }
    public function DestroyPage($id)
    {
        $data = Page::findOrFail($id);
        $res =  $data->delete();
        if ($res) {
            return back()->with('success', 'Page was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting Page');
        }
    }
    public function page()
    {
        $data['title'] = 'Web pages';
        $data['page'] = Page::latest()->get();
        return view('admin.web-control.page', $data);
    }
    public function ppage($id)
    {
        $page = Page::find($id);
        $page->status = 1;
        $page->save();
        return back()->with('success', 'Page was successfully published.');
    }
    public function unpage($id)
    {
        $page = Page::find($id);
        $page->status = 0;
        $page->save();
        return back()->with('success', 'Page has been unpublished.');
    }
    public function CreatePage(Request $request)
    {
        $data['title'] = $request->title;
        $data['content'] = Purifier::clean($request->content);
        $data['slug'] = str_slug($request->title);
        $res = Page::create($data);
        if ($res) {
            return redirect()->route('admin.page')->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Creating New Page');
        }
    }
    //

    //UI
    public function homepage()
    {
        $data['title'] = 'Homepage';
        return view('admin.web-control.home', $data);
    }
    public function Updatehomepage(Request $request)
    {
        $data = Design::findOrFail(1);
        $data->header_title = $request->header_title;
        $data->header_body = $request->header_body;
        $data->s1_title = $request->s1_title;
        $data->s2_title = $request->s2_title;
        $data->s3_title = $request->s3_title;
        $data->s3_body = $request->s3_body;
        $data->s6_title = $request->s6_title;
        $data->s6_body = $request->s6_body;
        $data->s7_title = $request->s7_title;
        $data->s7_body = $request->s7_body;        
        $data->s8_title = $request->s8_title;
        $data->s8_body = $request->s8_body;
        $res = $data->save();
        if ($res) {
            return back()->with('success', 'Update was Successful!');
        } else {
            return back()->with('alert', 'An error occured');
        }
    }

    //

    //Branch
    public function CreateBranch(Request $request)
    {
        $data['name'] = $request->name;
        $data['country'] = $request->country;
        $data['state'] = $request->state;
        $data['mobile'] = $request->mobile;
        $data['zip_code'] = $request->zip_code;
        $data['postal_code'] = $request->postal_code;
        $data['address'] = $request->address;
        $res = Branch::create($data);
        if ($res) {
            return redirect()->route('admin.branch')->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Creating New Branch');
        }
    }
    public function DestroyBranch($id)
    {
        $data = Branch::findOrFail($id);
        $res =  $data->delete();
        if ($res) {
            return back()->with('success', 'Branch was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting Branch');
        }
    }

    public function UpdateBranch(Request $request)
    {
        $mac = Branch::findOrFail($request->id);
        $mac['name'] = $request->name;
        $mac['country'] = $request->country;
        $mac['state'] = $request->state;
        $mac['mobile'] = $request->mobile;
        $mac['zip_code'] = $request->zip_code;
        $mac['postal_code'] = $request->postal_code;
        $mac['address'] = $request->address;
        $res = $mac->save();
        if ($res) {
            return redirect()->route('admin.branch')->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With Updating Faq');
        }
    }
    public function branch()
    {
        $data['title'] = 'Bank branches';
        $data['branch'] = Branch::latest()->get();
        return view('admin.web-control.branch', $data);
    }
    //

}
