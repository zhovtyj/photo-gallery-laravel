<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use File;
use Image;
use App\Gallery;
use App\Photo;

class PhotoController extends Controller
{
    public function uploadPhotos(Request $request)
    {
        $request->validate([
            'file.*' => 'required|file|mimetypes:image/gif,image/jpeg,image/pjpeg,image/png',
        ]);

        $gallery = Gallery::find($request->gallery_id);
        $order = $gallery->photos->count();

        foreach ($request->file as $file){
            $filename = rand(1,1000).md5(time()).'.'.$file->getClientOriginalExtension();
            $location = storage_path().'/app/public/galleries/' . $gallery->id . '/' . $filename;
            $img = Image::make($file)->interlace();

            $maxWidth = 2560; // your max width
            $maxHeight = 2560; // your max height
            if($img->height() > $maxHeight || $img->width() > $maxHeight){
                $img->height() > $img->width() ? $width=null : $height=null;
                $img->resize($maxWidth, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            //Check if folder exists before saving
            File::exists(storage_path('app/public/galleries')) or File::makeDirectory(storage_path('app/public/galleries'));
            File::exists(storage_path('app/public/galleries/' . $gallery->id)) or File::makeDirectory(storage_path('app/public/galleries/' . $gallery->id));
            $img->save($location);

            $photo['photoed_id'] = $gallery->id;
            $photo['photoed_type'] = get_class($gallery);
            $photo['order'] = $order++;
            $photo['title'] = null;
            $photo['url'] = '/storage/galleries/' . $gallery->id . '/' . $filename;
            $photo['file_name'] = $filename;
            $photo['description'] = null;
            $photo['width'] = $img->width();
            $photo['height'] = $img->height();
            $photo['size'] = $img->filesize();
            $photo['created_at'] = Carbon::now();
            $photos_arr[] = $photo;
        }

        foreach ($photos_arr as $photo_arr){
            $photos[] = Photo::create($photo_arr);
        }

        return $photos;
    }

    public function update(Request $request)
    {
        $photo = Photo::with('photoed')->find($request->photo['id']);

        $location = storage_path().'/app/public/galleries/' . $photo->photoed->id . '/' . $photo->file_name;
        $img = Image::make($request->photo['base64'])->interlace();

        $maxWidth = 2560; // your max width
        $maxHeight = 2560; // your max height
        if($img->height() > $maxHeight || $img->width() > $maxHeight){
            $img->height() > $img->width() ? $width=null : $height=null;
            $img->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        //Check if folder exists before saving
        File::exists(storage_path('app/public/galleries')) or File::makeDirectory(storage_path('app/public/galleries'));
        File::exists(storage_path('app/public/galleries/' . $photo->photoed->id)) or File::makeDirectory(storage_path('app/public/galleries/' . $photo->photoed->id));
        $img->save($location);

        $photo->url = '/storage/galleries/' . $photo->photoed->id . '/' . $photo->file_name . '?v='. time();
        $photo->width = $img->width();
        $photo->height = $img->height();
        $photo->size = $img->filesize();
        $photo->save();

        return $photo;
    }

    public function updateAttr(Request $request)
    {
        $photo = Photo::find($request->photo['id']);
        $photo->title = $request->photo['title'];
        $photo->description = $request->photo['description'];
        $photo->save();

        return $photo;
    }

    public function order(Request $request)
    {
        $gallery = Gallery::with(['photos'])->find($request->galleryId);
        $photo = $gallery->photos->where('order', $request->oldOrder)->first();
        $photo->order = $request->newOrder;

        $reorder = $request->newOrder < $request->oldOrder?'increment':'decrement';
        $between = $request->newOrder < $request->oldOrder?[$request->newOrder, $request->oldOrder]:[$request->oldOrder, $request->newOrder];

        $photos = Photo::whereIn('id', $gallery->photos->pluck('id')->toArray())
            ->where('id', '!=', $photo->id)
            ->whereBetween('order', $between)
            ->$reorder('order');
        $photo->save();

        return 'success';

    }

    public function status(Request $request, $id)
    {
        $photo = Photo::find($id);
        $photo->active = !$photo->active;
        $photo->save();

        return $photo;
    }

    public function destroy($id)
    {

        $photo = Photo::find($id);
        $filePath = storage_path('\app\public\\'.str_replace('/storage/', '', $photo->url));
        $filePath = str_replace('/', '\\', $filePath);
        try{
            unlink($filePath);
        }catch (\Exception $exception){
            echo('File doesn\'t exist');
        }

        $photo->delete();

        return 'success';
    }
}
