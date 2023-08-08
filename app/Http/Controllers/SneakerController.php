<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSneakerRequest;
use App\Http\Requests\UpdateSneakerRequest;
use App\Models\Sneaker;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SneakerController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $sneakers = Sneaker::with([
            'media' => function($query) {
                $query->select(['id','sneaker_id','imageUrl', 'smallImageUrl', 'thumbUrl']);
            }
        ])->get(['id','title', 'brand', 'colorway', 'gender', 'retailPrice', 'releaseDate']);

        return $this->success($sneakers, 200);
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
            'imageUrl' => $request->imageUrl,
            'smallImageUrl' => $request->smallImageUrl,
            'thumbUrl' => $request->thumbUrl,
        ]);

        return $this->success([
            'id' => $sneaker->id,
            'title' => $request->title,
            'brand' => $request->brand,
            'colorway' => $request->colorway,
            'gender' => $request->gender,
            'retailPrice' => $request->retailPrice,
            'releaseDate' => $request->releaseDate,
            'media' => [
                'id' => $media->id,
                'sneaker_id' => $media->sneaker_id,
                'imageUrl' => $media->imageUrl,
                'smallImageUrl' => $media->smallImageUrl,
                'thumbUrl' => $media->thumbUrl
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
        
        return $this->success($sneaker, 200);
    }


    public function update(UpdateSneakerRequest $request, string $id)
    {
        $request->validated($request->all());

        $sneaker = Sneaker::find($id);

        $sneaker->update($request->except(['imageUrl', 'smallImageUrl', 'thumbUrl']));

        $sneaker->media()->update([
            'imageUrl' => $request->imageUrl,
            'smallImageUrl' => $request->smallImageUrl,
            'thumbUrl' => $request->thumbUrl,
        ]);

        return $this->success([
            'id' => $sneaker->id,
            'title' => $request->title,
            'brand' => $request->brand,
            'colorway' => $request->colorway,
            'gender' => $request->gender,
            'retailPrice' => $request->retailPrice,
            'releaseDate' => $request->releaseDate,
            'media' => [
                'id' => $sneaker->media->id,
                'sneaker_id' => $sneaker->media->sneaker_id,
                'imageUrl' => $request->imageUrl,
                'smallImageUrl' => $request->smallImageUrl,
                'thumbUrl' => $request->thumbUrl
            ]
        ], 200);
    }


    public function destroy(string $id)
    {
        if(!Sneaker::destroy($id)) {
            return $this->error('','sneaker with id: '. $id . ' not found',404);
        }

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
