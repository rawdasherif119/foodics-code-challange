<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    private $statusCode;
    private $message;

    /**
     * Create a new resource instance.
     *
     * @param int    $code
     * @param string $message
     * @param $resource
     */
    public function __construct($statusCode, $message = null, $resource = [])
    {
        parent::__construct($resource);
        $this->wrap(false);
        $this->statusCode = $statusCode;
        $this->message    = $message;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'message' => $this->message ?? __('errors.somthing_wrong'),
        ];
    }

    /**
     * Customize the response for a request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->statusCode);
    }
}
