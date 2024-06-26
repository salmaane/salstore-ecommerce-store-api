<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserVisit;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Stevebauman\Location\Facades\Location;

class UserAnalyticsController extends Controller
{
    use ResponseTrait;

    public function newUsers($limit = 5) {
        $users = User::orderBy('created_at','desc')->take($limit)->get();

        foreach ($users as $user) {
            if ($user->profile) {
                $user->profile = Storage::disk('public')->url($user->profile);
            }
        }

        return $users;
    }

    public function captureVisit(Request $request, $userId = null) {
        $visitRecord = UserVisit::where('ip_address',$request->ip())->first();
        if( !$position = Location::get($request->ip()) ) {
            return $this;
        };

        if($visitRecord) {
            $visitRecord->update([
                'user_id' => $userId ?? $visitRecord->user_id,
                'user_agent' => $request->userAgent(),
                'visit_time' => now(),
                'country' => $position->countryName,
                'city' => $position->cityName,
            ]);
            return $visitRecord;
        }

        $newVisit = UserVisit::create([
            'user_id' =>  $userId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'visit_time' => now(),
            'country' => $position->countryName,
            'city' => $position->cityName,
        ]);
        return $newVisit;
    }

    public function usersVisits($limit = 10) {
        $userVisits = UserVisit::select(
                        'country',
                        DB::raw('count(country) as visits_number'),
                    )
                    ->groupBy('country')->orderBy('visits_number','desc')->take($limit)->get();

        return [
            'total' => UserVisit::count(),
            'countries' => $userVisits,
        ];
    }
}
