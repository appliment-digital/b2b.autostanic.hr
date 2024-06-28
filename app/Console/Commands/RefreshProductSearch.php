<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshProductSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product_search:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh product searches from MSSQL data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('product_searches')->delete();

        // DB::insert('
        //      insert into product_searches (product_id, search_text)
        //      select id, search_text from mssql_ProductSearchText
        //  ');

        // $this->info('Product searches have been refreshed successfully.');

        // return 0;
    }
}
