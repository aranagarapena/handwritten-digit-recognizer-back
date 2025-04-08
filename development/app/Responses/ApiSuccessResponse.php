<?php

namespace App\Responses;

use Illuminate\Http\Request;

class ApiSuccessResponse
{
    public $status;
    public $message;
    public $data;
    public $code;
    public $timestamp;
    public $path;

    public function __construct($message = "", $data = [], $code = 200, $path = "")
    {
        $this->status = "success";
        $this->message = $message;
        $this->data = $data;
        $this->code = $code;
        $this->timestamp = now()->toIso8601String();
        $this->path = $path ?: Request::url();
    }

    public function toArray()
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
            'code' => $this->code,
            'timestamp' => $this->timestamp,
            'path' => $this->path,
        ];
    }
}
