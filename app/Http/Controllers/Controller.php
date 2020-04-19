<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\File;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function setDelay($seconds = 1)
    {
        sleep($seconds);
    }

    protected function jsonResponse($customData, $statusCode = 200, $isSuccessful = true)
    {
        $data = [
            'status_code' => $statusCode,
            'success' => $isSuccessful,
        ];
        $data = array_merge($data, $customData);
        return response()->json([
            'data' => $data,
        ], $statusCode);
    }

    protected function clean($input, $allowedTags = null)
    {
        if ($allowedTags) {
            $filteredInput = strip_tags($input, $allowedTags);
        } else {
            $filteredInput = strip_tags($input);
        }
        $filteredInput = trim($filteredInput);
        // $filteredInput = str_replace("'", " ", $filteredInput);
        // $filteredInput = str_replace("`", " ", $filteredInput);
        $filteredInput = htmlspecialchars($filteredInput, ENT_QUOTES);
        return $filteredInput;
    }

    public function mkDir($route = null)
    {
        if ($route) {
            $imgRoute = public_path() . '/img/' . $route . '/';
        } else {
            $imgRoute = public_path() . '/img/';
        }
        $currentYear = date('Y');
        $currentMonth = date('m');
        if (!is_dir($imgRoute . $currentYear)) {
            File::makeDirectory($imgRoute . $currentYear, 0777, true, true);
        }
        if (!is_dir($imgRoute . $currentYear . '/' . $currentMonth)) {
            File::makeDirectory($imgRoute . $currentYear . '/' . $currentMonth, 0777, true, true);
        }
    }

    protected function makeArrayValuesUnique($array, $key)
    {
        $result = $keyArray = [];
        $i = 0;
        foreach ($array as $val) {
            if (!in_array($val[$key], $keyArray)) {
                $keyArray[$i] = $val[$key];
                $result[$i] = $val;
            }
            $i++;
        }
        return $result;
    }
}
