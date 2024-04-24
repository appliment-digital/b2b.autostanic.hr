<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebDatabaseController extends BaseController
{
    public function test()
    {
        $query = DB::connection('webDb')
            ->table('dbo.Category')
            ->select(
                'Id',
                'Name',
                'Description',
                'ParentCategoryId',
                'ProductCount'
            )
            ->get();

        return $query;
    }
}
