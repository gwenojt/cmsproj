<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuickLinks;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;


class QuickLinksController extends Controller
{

    public function table(){
        $page = QuickLinks::all();
        $list = array();
        $parent = "";
        foreach($page as $d){
            if($d->parent_link_id){
                $parent_v = QuickLinks::find($d->parent_link_id);
                if($parent_v){
                    $parent = $parent_v->title;
                }
            }
            $list[] = [
                'section' => $this->sectionValue($d->section),
                'link_category' => $this->categoryValue($d->link_category),
                'type' => $this->typeValue($d->type),
                'title' => $d->title,
                'filename' => $d->orig_filename,
                'link' => $d->link            ];
        }
        return $list;
        
    }

    public function typeValue($id){
        switch ($id) {
            case 1:
              return "Label";
              break;
            case 2:
              return "Clickable";
              break;
            
            default:
              return " ";
          }
    }

    public function categoryValue($id){
        switch ($id) {
            case 1:
              return "Label";
              break;
            case 2:
              return "Sub Label Level 1";
              break;
            
            case 3:
              return "Sub Label Level 2";
              break;
            
            case 4:
              return "Sub Label Level 3";
              break;
            
            default:
              return " ";
          }
    }

    public function sectionValue($id){
        switch ($id) {
            case 1:
              return "Current Awareness Services";
              break;
            case 2:
              return "ULS Forms";
              break;
            
            case 3:
              return "Virtual Libraries";
              break;
            
            case 4:
              return "Library Search Tools";
              break;

            case 5:
                return "Featured Links";
                break;
            
            default:
              return " ";
          }
    }

    public function saveCurAware(Request $request)
    {
        $user = Auth::user();
        $file = $request->file;

        $new = new QuickLinks();
        $new->parent_link_id = $request->parent_link_id;
        $new->link_category = $request->link_category;
        $new->type = $request->type;
        $new->section = $request->section;
        $new->title = $request->title;
        $new->link = $request->link;
        $new->user_id = auth()->user()->id;

        if ($file) {

            $filename = md5(time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();

            Storage::put('/public/quicklinks/' . $filename, File::get($file));

            $new->filename = $filename;
            $new->orig_filename = $file->getClientOriginalName();
        }

        $res = $new->save();

        if ($res) {
            return response()->json(['success' => true, 'message' => 'Data saved successfully']);
        } else {
            \Log::error('Failed to save data to the database', ['data' => $new->toArray()]);
            return response()->json(['success' => false, 'message' => 'Failed to save data to the database']);
        }
    }

    // public function saveCurAware(Request $request)
    // {
    //     $user = Auth::user();
    //     $file = $request->file;
    //     // if ($request->hasFile('file')) {
    //         // Generate a unique filename using md5 hash
    //         $filename = md5(time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
    
    //         Storage::put('/public/quicklinks/'.$filename,File::get($file));
    //         // $path = $file->storeAs('uploads', $filename, 'public');

    //         $new = new QuickLinks();
    //         $new->filename = $filename;
    //         $new->orig_filename = $file->getClientOriginalName();
    //         $new->parent_link_id = $request->parent_link_id;
    //         $new->link_category = $request->link_category;
    //         $new->type = $request->type;
    //         $new->section = $request->section;
    //         $new->title = $request->title;
    //         $new->link = $request->link;
    //         $new->user_id = auth()->user()->id;
    
    //         $res = $new->save();
    
    //         return response()->json(['success' => true, 'message' => 'File uploaded successfully']);
    //     // }
    
    //     // return response()->json(['success' => false, 'message' => 'No file provided']);
    // }
    

    public function deleteContent(Request $request)
    {
        $quickLink = QuickLinks::find($request->id);

        if (!$quickLink) {
            return response()->json(['success' => false, 'error' => 'QuickLink not found'], Response::HTTP_NOT_FOUND);
        }

        if ($quickLink->filename) {
            $filePath = storage_path("app/{$quickLink->filename}");
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $quickLink->delete();

        return response()->json(['success' => true, 'message' => 'QuickLink deleted successfully']);
    }
    

    public function getParent($section, $linkCategory)
    {

        $section = (int)$section;
        $linkCategory = (int)$linkCategory;
    

        $parent = QuickLinks::where('section', $section)
            ->where('link_category', $linkCategory - 1)
            ->get();
    
        return $parent;
    }
    
    // public function getParent($id){
    //     // dd($id);
    //     if($id == 5 || $id == 2){
    //         $par_cat = 1;
    //     }else{
    //         $par_cat = $id-1;
    //     }
    //     $parent = QuickLinks::where('link_category',$par_cat)->get();
    //     // dd($parent);
    //     return  $parent;
    // }

}
