<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends Controller
{
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * @param  StoreUserRequest  $request
     */
    public function register(StoreUserRequest $request): Response
    {
        $this->service->create($request->all());
        return response()->noContent(Response::HTTP_CREATED);
    }

    /**
     * @param  LoginRequest  $request
     * @param  ServerRequestInterface $serverRequest
     */
    public function login(LoginRequest $request, ServerRequestInterface $serverRequest):JsonResponse
    {
        return response()->json([
            'data' => $this->service->login($request->all(), $serverRequest),
        ]);
    }
}
