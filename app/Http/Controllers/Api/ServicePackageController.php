<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServicePackageRequest;
use App\Http\Requests\UpdateServicePackageRequest;
use App\Http\Resources\ServicePackageApiResource;
use App\Models\ServicePackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ServicePackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $packages = ServicePackage::query()
            ->when($request->integer('category'), fn (Builder $query, int $category) => $query->where('category_id', $category))
            ->get();

        return ServicePackageApiResource::collection($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServicePackageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ServicePackage $servicePackage)
    {
        return ServicePackageApiResource::make($servicePackage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServicePackageRequest $request, ServicePackage $servicePackage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServicePackage $servicePackage)
    {
        //
    }
}
