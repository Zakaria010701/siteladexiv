<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryApiResource;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontendCategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        /*$this->validate($request, [
            'branch' => 'required|exists:branches,id',
            'appointment_type' => 'required'
        ]);*/

        return CategoryApiResource::collection(Category::all());
    }
}
