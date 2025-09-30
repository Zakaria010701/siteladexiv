<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceApiResource;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FrontendServiceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|exists:categories,id',
        ]);

        $services = Service::query()
            ->when($request->integer('category'), fn (Builder $query, int $category) => $query->where('category_id', $category))
            ->get();

        return ServiceApiResource::collection($services);
    }
}
