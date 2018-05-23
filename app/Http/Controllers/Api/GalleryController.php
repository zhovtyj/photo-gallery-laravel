<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Gallery;


class GalleryController extends Controller
{
    public function index()
    {
        return Gallery::with('photos')->latest()->get()
            ->map(function ($gallery, $key) {
                $gallery->active = $gallery->active?true:false;
                $gallery->photos->map(function ($photo, $key) {
                    $photo->active = $photo->active?true:false;
                    return $photo;
                });
                return $gallery;
            });
    }

    public function store(Request $request)
    {
        $gallery = Gallery::create([
            'title' => $request->gallery['title'],
            'description' => $request->gallery['description'],
        ]);

        $gallery->photos = [];
        return $gallery;
    }

    public function show($id)
    {
        $gallery = Gallery::with('photos')->find($id);
        $gallery->photos->map(function ($photo, $key) {
            $photo->active = $photo->active?true:false;
            return $photo;
        });

        return $gallery;
    }

    public function update(Request $request, $id)
    {
        $gallery = Gallery::with('photos')->find($id);
        $gallery->title = $request->gallery['title'];
        $gallery->description = $request->gallery['description'];
        $gallery->save();

        return $gallery;
    }

    public function status(Request $request, $id)
    {
        $gallery = Gallery::find($id);
        $gallery->active = !$gallery->active;
        $gallery->save();

        return $gallery;
    }

    public function destroy($id)
    {
        $gallery = Gallery::find($id);
        $gallery->delete();

        return 'success';
    }
}
