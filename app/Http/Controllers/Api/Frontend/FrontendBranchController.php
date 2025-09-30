<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\BranchApiResource;
use App\Models\Branch;
use Illuminate\Http\Request;

class FrontendBranchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        /*$this->validate($request, [
            'frontend' => 'required',
        ]);*/

        return BranchApiResource::collection(Branch::all());
    }
}
