<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\OrderService;
use App\Http\Requests\OrderRequest;

class OrderController extends Controller
{
    protected $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    /**
     * @param  OrderRequest  $request
     */
    public function store(OrderRequest $request): Response
    {
        $this->service->store($request->validated());
        return response()->noContent(Response::HTTP_OK);
    }
}
