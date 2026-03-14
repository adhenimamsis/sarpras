<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base Controller untuk SimSarpras Puskesmas Bendan.
 * Mengintegrasikan trait standar Laravel untuk validasi dan otorisasi.
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Helper Method: Response sukses standar untuk sistem.
     * Berguna jika nanti Bos membuat fitur AJAX atau API Integration.
     */
    protected function jsonSuccess($message = 'Operasi berhasil', $data = [], $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Helper Method: Response error standar.
     */
    protected function jsonError($message = 'Terjadi kesalahan', $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }
}
