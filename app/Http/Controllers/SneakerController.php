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
        return response()->json(
            Sneaker::with([
                'media' => function($query) {
                    $query
                    ->select(['id','sneaker_id','imageUrl', 'smallImageUrl', 'thumbUrl']);
                }
            ])->get(['id','title', 'brand', 'colorway', 'gender', 'retailPrice', 'releaseDate']), 200);
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
        ], 'sneaker added.', 201);
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
        
        return response()->json($sneaker, 200);
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
        ], 'Sneaker updated.', 200);
    }


    public function destroy(string $id)
    {
        if(!Sneaker::destroy($id)) {
            return $this->error('','sneaker with id: '. $id . ' not found',404);
        }

        return response()->json([
            "message" => "sneaker deleted.",
        ],200);
    }

    
    public function search($name) {
        $sneaker = Sneaker::with('media:id,sneaker_id,imageUrl,smallImageUrl,thumbUrl')
                            ->where('title', 'like', '%'. $name .'%')
                            ->get(['id', 'title', 'brand', 'colorway', 'gender', 'retailPrice', 'releaseDate']);

        return response()->json($sneaker, 200); 
    }
}
