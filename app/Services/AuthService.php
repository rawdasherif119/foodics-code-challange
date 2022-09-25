<?php

namespace App\Services;

use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ErrorResource;
use App\Traits\AuthenticatesUsersApi;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthService extends BaseService
{
    use AuthenticatesUsersApi;

    protected $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param  array $data
     * @param  ServerRequestInterface $serverRequest
     */
    public function login($data, $serverRequest)
    {
        $user = $this->repo->findBy('email', $data['email']);
        if ($user) {
            if (!Hash::check($data['password'], $user->getAuthPassword())) {
                return new ErrorResource(Response::HTTP_UNAUTHORIZED, __('auth.invalid_credentials'));
            }
            return $this->requestTokenToLogin($serverRequest, $data);
        }
        return new ErrorResource(Response::HTTP_UNAUTHORIZED, __('auth.invalid_credentials'));
    }

    /**
     * @param  array $data
     * @param  ServerRequestInterface $serverRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestTokenToLogin($serverRequest, $data): array
    {
        $result = $this->tokenRequest($serverRequest, $data, false);
        if (($result['statusCode'] == Response::HTTP_OK)) {
            return $result['response'];
        }
        return new ErrorResource($result['statusCode'], $result['response']['error_description']);
    }
}
