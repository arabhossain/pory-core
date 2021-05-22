<?php


namespace App\Services;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\ValidationException;

trait ApiReasonable
{
    /**
     * @param JsonResource $resource
     * @param null $message
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function respondWithResource(JsonResource $resource, $message = null, $statusCode = 200, $headers = []): JsonResponse
    {
        return $this->apiResponse(
            [
                'success' => true,
                'result' => $resource,
                'message' => $message
            ], $statusCode, $headers
        );
    }

    /**
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @return array
     */
    public function parseGivenData($data = [], $statusCode = 200, $headers = []): array
    {
        $responseStructure = [
            'success' => $data['success'],
            'message' => $data['message'] ?? null,
            'result' => $data['result'] ?? null,
        ];
        if (isset($data['errors']))
            $responseStructure['errors'] = $data['errors'];

        if (isset($data['status']))
            $statusCode = $data['status'];

        if ($data['exception'] instanceof Exception) {
            if (config('app.env') !== 'production') {
                $responseStructure['exception'] = [
                    'message' => $data['exception']->getMessage(),
                    'file' => $data['exception']->getFile(),
                    'line' => $data['exception']->getLine(),
                    'code' => $data['exception']->getCode(),
                    'trace' => $data['exception']->getTrace(),
                ];
            }

            if ($statusCode === 200)
                $statusCode = 500;
        }

        if ($data['success'] === false) {
            $responseStructure['error_code'] = 1;
            if (isset($data['error_code']))
                $responseStructure['error_code'] = $data['error_code'];
        }

        return ["content" => $responseStructure, "statusCode" => $statusCode, "headers" => $headers];
    }

    /**
     * Just a wrapper to facilitate abstract
     * Return generic json response with the given data.
     * @param     $data
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function apiResponse($data = [], $statusCode = 200, $headers = []): JsonResponse
    {
        $result = $this->parseGivenData($data, $statusCode, $headers);
        return response()->json(
            $result['content'], $result['statusCode'], $result['headers']
        );
    }

    /**
     * Just a wrapper to facilitate abstract
     * @param ResourceCollection $resourceCollection
     * @param null $message
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function respondWithResourceCollection(ResourceCollection $resourceCollection, $message = null, $statusCode = 200, $headers = []): JsonResponse
    {
        return $this->apiResponse(
            [
                'success' => true,
                'result' => $resourceCollection->response()->getData()
            ], $statusCode, $headers
        );
    }

    /**
     * Respond with success.
     * @param string $message
     * @return JsonResponse
     */
    protected function respondSuccess($message = ''): JsonResponse
    {
        return $this->apiResponse(['success' => true, 'message' => $message]);
    }

    /**
     * Respond with created.
     * @param $data
     * @return JsonResponse
     */
    protected function respondCreated($data): JsonResponse
    {
        return $this->apiResponse($data, 201);
    }

    /**
     * Respond with no content.
     * @param string $message
     * @return JsonResponse
     */
    protected function respondNoContent($message = 'No Content Found'): JsonResponse
    {
        return $this->apiResponse(['success' => false, 'message' => $message], 200);
    }

    /**
     * Respond with unauthorized.
     * @param string $message
     * @return JsonResponse
     */
    protected function respondUnAuthorized($message = 'Unauthorized'): JsonResponse
    {
        return $this->respondError($message, 401);
    }

    /**
     * Respond with error.
     * @param $message
     * @param int $statusCode
     * @param Exception|null $exception
     * @param int $error_code
     * @return JsonResponse
     */
    protected function respondError($message, int $statusCode = 400, Exception $exception = null, int $error_code = 1): JsonResponse
    {
        return $this->apiResponse(
            [
                'success' => false,
                'message' => $message ?? 'There was an internal error, Pls try again later',
                'exception' => $exception,
                'error_code' => $error_code
            ], $statusCode
        );
    }

    /**
     * Respond with forbidden.
     * @param string $message
     * @return JsonResponse
     */
    protected function respondForbidden($message = 'Forbidden'): JsonResponse
    {
        return $this->respondError($message, 403);
    }

    /**
     * Respond with not found.
     * @param string $message
     * @return JsonResponse
     */
    protected function respondNotFound($message = 'Not Found'): JsonResponse
    {
        return $this->respondError($message, 404);
    }

    /**
     * Respond with internal error.
     * @param string $message
     * @return JsonResponse
     */
    protected function respondInternalError($message = 'Internal Error'): JsonResponse
    {
        return $this->respondError($message, 500);
    }

    /**
     * @param ValidationException $exception
     * @return JsonResponse
     */
    protected function respondValidationErrors(ValidationException $exception): JsonResponse
    {
        return $this->apiResponse(
            [
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->errors()
            ],
            422
        );
    }
}
