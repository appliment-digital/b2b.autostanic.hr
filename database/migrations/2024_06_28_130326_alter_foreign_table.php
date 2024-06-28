<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP FOREIGN TABLE mssql_ProductSearchText');

        DB::statement("create FOREIGN TABLE mssql_ProductSearchText     
            (                                                              
                id integer,
                search_text text
            )
            SERVER mssql_server OPTIONS 
            (schema 'dbo', sql_query 'select [Product].[Id] as [id], CONCAT( 
            Product.Name, CASE WHEN Product.Sku <> '''' 
            THEN CONCAT('', '', Product.Sku) ELSE '''' END, 
            CASE WHEN Product.ManufacturerName <> '''' T
            HEN CONCAT('', '', Product.ManufacturerName) 
            ELSE '''' END, CASE WHEN ProductSearch.EntityValue <> '''' 
            THEN CONCAT('', '', ProductSearch.EntityValue) ELSE '''' END, 
            CASE WHEN EXISTS ( SELECT 1 FROM Product_OEMCode_Mapping 
            WHERE Product_OEMCode_Mapping.ProductId = Product.Id 
            AND Product_OEMCode_Mapping.IsOEMCode = 1 ) 
            THEN CONCAT('', '', ( SELECT STRING_AGG(Product_OEMCode_Mapping.OEMCodeDenormalized, '', '') 
            FROM Product_OEMCode_Mapping WHERE Product_OEMCode_Mapping.ProductId = Product.Id )) ELSE '''' END ) 
            AS search_text from [dbo].[Product] 
            left join [B2B].[ProductSearch] on [Product].[Id] = [ProductSearch].[ProductId] 
            where [Product].[Deleted] = 0 and [Product].[Published] = 1 and [Product].[Price] > 0');");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
