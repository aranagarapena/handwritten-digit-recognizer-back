<?php

namespace App\Responses;
use Illuminate\Http\Request;

class ApiErrorResponse
{
    public $status;
    public $message;
    public $errors;
    public $code;
    public $timestamp;
    public $path;

    public function __construct($message = "", $errors = [], $code = 500, $url="")
    {
        $this->status = "error";
        $this->message = $message;
        $this->errors = $errors;
        $this->code = $code;
        $this->timestamp = now()->toIso8601String();
        $this->path = $url;
    }

    public function toArray()
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'errors' => $this->errors,
            'code' => $this->code,
            'timestamp' => $this->timestamp,
            'path' => $this->path,
        ];
    }
}
