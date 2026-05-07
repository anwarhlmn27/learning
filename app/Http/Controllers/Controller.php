<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Exception;

abstract class Controller
{
    /**
     * Handle exceptions globally, specifically for foreign key constraint violations.
     *
     * @param Exception $e
     * @param string $defaultMsg
     * @return string
     */
    protected function handleException(Exception $e, $defaultMsg = 'Operasi gagal dilakukan.')
    {
        if ($e instanceof QueryException) {
            $errorCode = $e->errorInfo[1] ?? null;
            if ($errorCode == 1451) {
                // Parse the table name from the error message
                preg_match('/a foreign key constraint fails \([^\.]+\.[\`\"\']?([^\`\"\'\,]+)[\`\"\']?\,/', $e->getMessage(), $matches);
                $tableName = $matches[1] ?? 'lain';
                return "Data tidak bisa dihapus karena masih ada koneksi dengan data pada tabel: {$tableName}";
            }
        }
        return $defaultMsg . ' ' . $e->getMessage();
    }
}
