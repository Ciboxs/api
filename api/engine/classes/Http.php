<?php

declare(strict_types=1);

namespace Api\Engine\Classes;

class Http {


    # **
    public function responseCode(int $code = 200, string $message = ''): array
    {

        switch ($code) {
            case 200:
                $status = true;
                $message = 'Ok';
            break;
            case 204:
                $status = true;
                $message = 'No Content';
            break;
            case 400:
                $status = false;
                $message = 'Bad Request';
            break;
            case 404:
                $status = false;
                $message = 'Not found';
            break;
            default:
                $status = true;
                $message = 'Ok';
        }

        http_response_code($code);
        $result = [
            'code' => $code,
            'status' => $status,
            'message' => $message
        ];

        if (!in_array($code, [200])) {
            print json_encode($result);
            exit;
        }

        return $result;
    }


}



?>