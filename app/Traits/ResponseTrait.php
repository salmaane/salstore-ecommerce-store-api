<?php 

namespace App\Traits;

trait ResponseTrait {
    protected function success($data, $code = 200) {
        return response()->json($data, $code);
    }

    protected function error($data, $code) {
        return response()->json($data, $code);
    }
}