<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebDatabaseController extends BaseController
{
    public function test()
    {
        $schemaExists = DB::connection('sqlsrv')->select("SELECT SCHEMA_ID('dbo') AS 'Exists'");
        if (!empty($schemaExists) && isset($schemaExists[0]->Exists) && $schemaExists[0]->Exists !== null) {
            if ($schemaExists[0]->Exists) {
                return "The 'dbo' schema exists in the database.";
            } else {
                return "The 'dbo' schema does not exist in the database.";
            }
        } else {
            return "Failed to determine if the 'dbo' schema exists.";
        }
    }
}
