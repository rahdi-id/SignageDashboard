<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\PromotionMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Pawlox\VideoThumbnail\Facade\VideoThumbnail;

class PromotionMediaController extends Controller
{
    public function index($id)
    {
        $promotion = Promotion::find($id);
        return view('promotion_medias.index', ['title' => 'Promotion Media', 'promotion' => $promotion]);
    }

    public function create($id)
    {
        return view('promotion_medias.create', ['title' => 'Add Promotion Media', 'promotionId' => $id]);
    }

    public function store($id, Request $request)
    {
        $imageExtensions = ['jpg', 'png', 'svg', 'jpeg'];
        if ($request->media) {
            foreach ($request->media as $media) {
                $filename = rand(1, 9999) . time() . '.' . $media->getClientOriginalExtension();

                $promotionMedia = new PromotionMedia();
                $promotionMedia->promotion_id = $id;
                $promotionMedia->title = pathinfo($media->getClientOriginalName(), PATHINFO_FILENAME);
                if (in_array($media->getClientOriginalExtension(), $imageExtensions)) {
                    $media->move(public_path('images'), $filename);
                    $promotionMedia->type = 'Image';
                } else {
                    $media->move(public_path('videos'), $filename);
                    $promotionMedia->type = 'Video';
                }
                $promotionMedia->name = $filename;
                $promotionMedia->save();
            }
        }

        if ($request->youtube) {
            foreach ($request->youtube as $youtube) {
                $promotionMedia = new PromotionMedia();

                $promotionMedia->promotion_id = $id;
                $promotionMedia->name = 'youtube';
                $promotionMedia->title = "Youtube Video";
                $promotionMedia->type = 'Video';
                $promotionMedia->url_youtube = $youtube;

                $promotionMedia->save();
            }
        }

        return redirect()->route('promotion-media.index', $id)->withSuccess('Created Successfully');
    }


    public function data($id)
    {
        $promotionMedias = PromotionMedia::where('promotion_id', $id)->get();
        $data['data'] = $promotionMedias;
        return response()->json(
            $data
        );
    }

    public function destroy($id, $mediaId)
    {
        $promotionMedia = PromotionMedia::find($mediaId);
        if ($promotionMedia->type == 'Image') {
            File::delete('images/' . $promotionMedia->name);
        } else {
            if ($promotionMedia->url_youtube == null) {
                File::delete('videos/' . $promotionMedia->name);
            }
        }
        $promotionMedia->delete();
        return redirect()->route('promotion-media.index', $id)->withSuccess('Deleted Successfully');
    }
}
