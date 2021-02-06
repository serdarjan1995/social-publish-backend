<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{

    protected function require_login($message)
    {
        return response()->json([
            'status' => "Unauthorized",
            'locale' => app()->getLocale(),
            'errors' => true,
            'login_required' => true,
            'data' => [
                'message' => $message,
            ]
        ], isset(JsonResponse::$statusTexts[401]) ? 401 : JsonResponse::HTTP_UNAUTHORIZED);
    }

    protected function fail($message = null, $errors = [])
    {
        $message = $this->ifMessageObjectReturnString($message);
        return response()->json([
            'status' => trans('api.failure'),
            'errors' => true,
            'locale' => app()->getLocale(),
            'data' =>
                array_merge(
                    ['message' => $message ? $message : trans('api.failure_message_auto')],
                    $errors)
        ],JsonResponse::HTTP_FORBIDDEN);
    }


    protected function success($message = null,$data = [])
    {
        $message = $this->ifMessageObjectReturnString($message);
        return response()->json([
            'status' => trans('api.success'),
            'errors' => false,
            'locale' => app()->getLocale(),
            'data' =>
            array_merge(
                ['message' => $message ? $message : "OK"],
                $data)
        ], JsonResponse::HTTP_OK);
    }

    protected function badRequest($message = null)
    {
        $message = $this->ifMessageObjectReturnString($message);
        return response()->json([
            'status' => trans('api.bad_request'),
            'error' => false,
            'locale' => app()->getLocale(),
            'data' => [
                'message' => $message ? $message : trans('api.bad_request_message_auto'),
            ]
        ], JsonResponse::HTTP_BAD_REQUEST);
    }


    protected function notFound($message = null)
    {
        $message = $this->ifMessageObjectReturnString($message);
        return response()->json([
            'status' => trans('app.not_found'),
            'errors' => false,
            'locale' => app()->getLocale(),
            'data' => [
                'message' => $message ? $message : trans('api.not_found_message_auto'),
            ]
        ], JsonResponse::HTTP_NOT_FOUND);
    }

    protected function unprocessableEntity($message = null)
    {
        $message = $this->ifMessageObjectReturnString($message);
        return response()->json([
            'status' => trans('api.failure'),
            'errors' => false,
            'locale' => app()->getLocale(),
            'data' => [
                'message' => $message ? $message : trans('api.unprocessable_entity'),
            ]
        ], JsonResponse::HTTP_NOT_FOUND);
    }


    protected function forbidden($message = null)
    {
        $message = $this->ifMessageObjectReturnString($message);
        return response()->json([
            'status' => trans('api.forbidden'),
            'errors' => false,
            'locale' => app()->getLocale(),
            'data' => [
                'message' => $message ? $message : trans('api.forbidden_message_auto'),
            ]
        ],JsonResponse::HTTP_FORBIDDEN);
    }

    protected function unauthorized($message = null)
    {
        $message = $this->ifMessageObjectReturnString($message);
        return response()->json([
            'status' => trans('api.unauthorized'),
            'errors' => false,
            'locale' => app()->getLocale(),
            'data' => [
                'message' => $message ? $message : trans('api.unauthorized_message_auto'),
            ]
        ],JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function ifMessageObjectReturnString($message){
        if ($message instanceof JsonResponse){
            return $message->getdata()->message;
        }
        return $message;
    }
}
