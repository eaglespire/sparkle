<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;


class LanguageController extends Controller
{
    public function languages()
    {
        $data['title']='Languages';
        $data['lang']=Language::all();
        return view('admin.user.lang', $data);
    }
    public function deletelanguage($id)
    {
        $lang = Language::findOrFail($id);
        @unlink(resource_path('lang/') . $lang->code . '.json');
        $lang->delete();
        return back()->with('success', 'Deleted Successfully');
    }
    public function Blocklanguage($id)
    {
        $lang=Language::find($id);
        $lang->status=1;
        $lang->save();
        return back()->with('success', 'Language has been suspended.');
    } 
    public function Unblocklanguage($id)
    {
        $lang=Language::find($id);
        $lang->status=0;
        $lang->save();
        return back()->with('success', 'language was successfully unblocked.');
    }    
    public function editlanguage($id)
    {
        $data['castro']=$lang=Language::whereid($id)->first();
        $data['title']='Update '.$lang->name.' Keywords';
        $arr=file_get_contents(resource_path('lang/').strtolower($lang->code).'.json');
        $data['json']=json_decode($arr, true);
        return view('admin.user.lang-edit', $data);
    }    
    public function updatelanguage(Request $request)
    {
        $lang=Language::find($request->id);
        $content = json_encode($request->keys);
        if ($content === 'null') {
            return back()->with('alert', 'At Least One Field Should Be Fill-up');
        }
        file_put_contents(resource_path('lang/') . strtolower($lang->code)  . '.json', $content);
        return back()->with('success', 'Update Successfully');
    }
    public function storelanguage(Request $request)
    {
        $lang=explode("*", $request->language);
        $check=Language::wherecode($lang[0])->count();
        if($check==1){
            return back()->with('alert', 'Already Added');
        }else{
            $data = file_get_contents(resource_path('lang/') . 'en.json');
            $json_file = trim(strtolower($lang[0])) . '.json';
            $path = resource_path('lang/') . $json_file;
            File::put($path, $data);
            $sav=new Language();
            $sav->name=$lang[1];
            $sav->code=$lang[0];
            $sav->save();
            return back()->with('success', 'Created Successfully');
        }
    }

}