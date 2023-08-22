<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSneakerRequest;
use App\Http\Requests\UpdateSneakerRequest;
use App\Models\Sneaker;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SneakerController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {   
        $limit = $request->limit ?? 10;

        $sneakers = Sneaker::with([
                'media' => function($query) {
                    $query->select(['id','sneaker_id','imageUrl', 'smallImageUrl', 'thumbUrl']);
                }
        ])->paginate($limit, ['id','title', 'brand', 'colorway', 'gender', 'retailPrice', 'releaseDate']);
        
        foreach($sneakers as $sneaker) {
            if(Storage::disk('public')->exists($sneaker->media->thumbUrl)) {
                $sneaker->media->thumbUrl = Storage::disk('public')->url($sneaker->media->thumbUrl);
            }
            if (Storage::disk('public')->exists($sneaker->media->imageUrl)) {
                $sneaker->media->imageUrl = Storage::disk('public')->url($sneaker->media->imageUrl);
            }
            if (Storage::disk('public')->exists($sneaker->media->smallImageUrl)) {
                $sneaker->media->smallImageUrl = Storage::disk('public')->url($sneaker->media->smallImageUrl);
            }
        }

        return $this->success(($sneakers), 200);
    }


    public function store(StoreSneakerRequest $request)
    {
        $request->validated($request->all());

        $sneaker = Sneaker::create([
            'title' => $request->title,
            'brand' => $request->brand,
            'colorway' => $request->colorway,
            'gender' => $request->gender,
            'retailPrice' => $request->retailPrice,
            'releaseDate' => $request->releaseDate,
        ]);

        $media = $sneaker->media()->create([
            'sneaker_id' => $sneaker->id,
            'imageUrl' => $this->storeImage($request, 'imageUrl'),
            'smallImageUrl' => $this->storeImage($request, 'smallImageUrl'),
            'thumbUrl' => $this->storeImage($request, 'thumbUrl'),
        ]);

        return $this->success([
            'id' => $sneaker->id,
            'title' => $sneaker->title,
            'brand' => $sneaker->brand,
            'colorway' => $sneaker->colorway,
            'gender' => $sneaker->gender,
            'retailPrice' => $sneaker->retailPrice,
            'releaseDate' => $sneaker->releaseDate,
            'media' => [
                'id' => $media->id,
                'sneaker_id' => $media->sneaker_id,
                'imageUrl' => Storage::disk('public')->url($media->imageUrl),
                'smallImageUrl' => Storage::disk('public')->url($media->smallImageUrl),
                'thumbUrl' => Storage::disk('public')->url($media->thumbUrl),
            ]
        ], 201);
    }


    public function show(string $id)
    {
        $sneaker = Sneaker::with([
            'media' => function ($query) {
                $query->select(['id', 'sneaker_id', 'imageUrl', 'smallImageUrl', 'thumbUrl']);
            }
        ])->find($id, ['id', 'title', 'brand', 'colorway', 'gender', 'retailPrice', 'releaseDate']);

        if(!$sneaker) {
            return $this->error('', 'No sneaker found with id: '. $id, 404);
        }

        if (Storage::disk('public')->exists($sneaker->media->thumbUrl)) {
            $sneaker->media->thumbUrl = Storage::disk('public')->url($sneaker->media->thumbUrl);
        }
        if (Storage::disk('public')->exists($sneaker->media->imageUrl)) {
            $sneaker->media->imageUrl = Storage::disk('public')->url($sneaker->media->imageUrl);
        }
        if (Storage::disk('public')->exists($sneaker->media->smallImageUrl)) {
            $sneaker->media->smallImageUrl = Storage::disk('public')->url($sneaker->media->smallImageUrl);
        }
        
        return $this->success($sneaker, 200);
    }


    public function update(UpdateSneakerRequest $request, string $id)
    {
        $request->validated($request->all());

        $sneaker = Sneaker::find($id);

        if(!$sneaker) {
            return $this->error(['message' => "sneaker (id: $id) not found"], 404);
        }

        $sneaker->update($request->except(['imageUrl', 'smallImageUrl', 'thumbUrl']));
        $sneaker->media()->update([
            'thumbUrl' => $request->thumbUrl ? $this->storeImage($request,'thumbUrl') : $sneaker->media->thumbUrl,
            'smallImageUrl' => $request->smallImageUrl ? $this->storeImage($request, 'smallImageUrl') : $sneaker->media->smallImageUrl,
            'imageUrl' => $request->imageUrl ? $this->storeImage($request, 'imageUrl') : $sneaker->media->imageUrl,
        ]);

        if (Storage::disk('public')->exists($sneaker->media->thumbUrl)) {
            $sneaker->media->thumbUrl = Storage::disk('public')->url($sneaker->media->thumbUrl);
        }
        if (Storage::disk('public')->exists($sneaker->media->imageUrl)) {
            $sneaker->media->imageUrl = Storage::disk('public')->url($sneaker->media->imageUrl);
        }
        if (Storage::disk('public')->exists($sneaker->media->smallImageUrl)) {
            $sneaker->media->smallImageUrl = Storage::disk('public')->url($sneaker->media->smallImageUrl);
        }

        return $this->success([
            'id' => $sneaker->id,
            'title' => $sneaker->title,
            'brand' => $sneaker->brand,
            'colorway' => $sneaker->colorway,
            'gender' => $sneaker->gender,
            'retailPrice' => $sneaker->retailPrice,
            'releaseDate' => $sneaker->releaseDate,
            'media' => [
                'id' => $sneaker->media->id,
                'sneaker_id' => $sneaker->media->sneaker_id,
                'imageUrl' => $sneaker->media->imageUrl,
                'smallImageUrl' => $sneaker->media->smallImageUrl,
                'thumbUrl' => $sneaker->media->thumbUrl
            ]
        ], 200);
    }


    public function destroy(string $id)
    {
        $sneaker = Sneaker::with([
            'media' => function ($query) {
                $query->select(['id', 'sneaker_id', 'imageUrl', 'smallImageUrl', 'thumbUrl']);
            }
        ])->find($id, ['id', 'title', 'brand', 'colorway', 'gender', 'retailPrice', 'releaseDate']);

        if(!Sneaker::destroy($id)) {
            return $this->error('sneaker with id: '. $id . ' not found',404);
        }

        Storage::disk('public')->delete([
            $sneaker->media->thumbUrl,
            $sneaker->media->smallImageUrl,
            $sneaker->media->imageUrl,
        ]);

        return $this->success([
            "message" => "sneaker deleted.",
        ],200);
    }

    
    public function search($name) {
        $sneaker = Sneaker::with('media:id,sneaker_id,imageUrl,smallImageUrl,thumbUrl')
                            ->where('title', 'like', '%'. $name .'%')
                            ->get(['id', 'title', 'brand', 'colorway', 'gender', 'retailPrice', 'releaseDate']);

        return $this->success($sneaker, 200); 
    }


    private function storeImage($request, $imageName) {
        $file = $request->file($imageName);
        $path = $file->store('productImages', 'public');
        return $path;
    }


    public function storeSneakers(Request $request) { // just for seeding
        $sneakers = $request->all()['sneakers'];

        foreach($sneakers as $sneaker) {
            $insertedSneaker = Sneaker::create([
                'title' => $sneaker['title'],
                'brand' => $sneaker['brand'],
                'colorway' => $sneaker['colorway'],
                'gender' => $sneaker['gender'],
                'retailPrice' => $sneaker['retailPrice'],
                'releaseDate' => $sneaker['releaseDate'],
            ]);

            $insertedSneaker->media()->create([
                'sneaker_id' => $insertedSneaker->id,
                'imageUrl' => $sneaker['media']['imageUrl'],
                'smallImageUrl' => $sneaker['media'][ 'smallImageUrl'],
                'thumbUrl' => $sneaker['media']['thumbUrl'],
            ]);
        }
        
        return $this->success(["message"=>"inserted successfully"],201);
    }
}
