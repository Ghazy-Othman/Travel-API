<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToursListRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;

class TourController extends Controller
{
    //
    public function index(Travel $travel, ToursListRequest $request)
    {
        // Tour::factory(25)->create(['travel_id' => $travel->id]) ;

        $tours = $travel->tours()
            ->when($request->dateFrom, function ($query) use ($request) {
                $query->where('starting_date', '>=', $request->dateFrom);
            })
            ->when($request->dateTo, function ($query) use ($request) {
                $query->where('starting_date', '<=', $request->dateTo);
            })
            ->when($request->priceFrom, function ($query) use ($request) {
                $query->where('price', '>=', $request->priceFrom * 100);
            })
            ->when($request->priceTo, function ($query) use ($request) {
                $query->where('price', '<=', $request->priceTo * 100);
            })
            ->when($request->sortBy && $request->sortOrder, function ($query) use ($request) {
                $query->orderBy($request->sortBy, $request->sortOrder);
            })
            ->orderBy('starting_date')
            ->paginate()->withQueryString();

        // return $tours ;
        return TourResource::collection($tours);
    }

    // public function index(Travel $travel, Request $request)
    // {

    //     // Get all tours
    //     $query = $travel->tours();

    //     // Filter date To
    //     if (request('dateFrom')) {
    //         $query->where('starting_date', '>=', request('dateFrom'));
    //     }

    //     // Filter date To
    //     if (request('dateTo')) {
    //         $query->where('ending_date', '<=', request('dateTo'));
    //     }

    //     // Filter price From
    //     if (request('priceFrom')) {
    //         $query->where('price', '>=', request('priceFrom'));
    //     }

    //     // Filter price From
    //     if (request('priceTo')) {
    //         $query->where('price', '<=', request('priceTo'));
    //     }

    //     $tours = $query
    //     ->orderBy('starting_date')
    //     ->paginate(10);

    //     return TourResource::collection($tours);
    // }
}
