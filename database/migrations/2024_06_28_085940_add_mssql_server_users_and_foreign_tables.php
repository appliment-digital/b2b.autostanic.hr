<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\String\b;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION odbc_fdw;');

        DB::statement("CREATE SERVER mssql_server
            FOREIGN DATA WRAPPER odbc_fdw
            OPTIONS (dsn 'MSSQL_ODBC');");

        $userExists = DB::select(
            "SELECT * FROM pg_roles WHERE rolname='b2b_autostanic_hr_trial'"
        );
        if (!empty($userExists)) {
            DB::statement(
                "CREATE USER MAPPING FOR b2b_autostanic_hr_trial SERVER mssql_server OPTIONS (odbc_uid 'appliment', odbc_pwd 'w1nvm1098#2');"
            );
        }

        $userExists = DB::select(
            "SELECT * FROM pg_roles WHERE rolname='postgres'"
        );
        if (!empty($userExists)) {
            DB::statement(
                "CREATE USER MAPPING FOR postgres SERVER mssql_server OPTIONS (odbc_uid 'appliment', odbc_pwd 'w1nvm1098#2');"
            );
        }

        $userExists = DB::select(
            "SELECT * FROM pg_roles WHERE rolname='homestead'"
        );
        if (!empty($userExists)) {
            DB::statement(
                "CREATE USER MAPPING FOR homestead SERVER mssql_server OPTIONS (odbc_uid 'appliment', odbc_pwd 'w1nvm1098#2');"
            );
        }

        $userExists = DB::select(
            "SELECT * FROM pg_roles WHERE rolname='b2b_autostanic_hr_test'"
        );
        if (!empty($userExists)) {
            DB::statement(
                "CREATE USER MAPPING FOR b2b_autostanic_hr_test SERVER mssql_server OPTIONS (odbc_uid 'appliment', odbc_pwd 'w1nvm1098#2');"
            );
        }

        $userExists = DB::select(
            "SELECT * FROM pg_roles WHERE rolname='forge'"
        );
        if (!empty($userExists)) {
            DB::statement(
                "CREATE USER MAPPING FOR forge SERVER mssql_server OPTIONS (odbc_uid 'appliment', odbc_pwd 'w1nvm1098#2');"
            );
        }

        DB::statement("create FOREIGN TABLE mssql_ProductSearchText     
            (                                                              
                id integer,
                search_text text
            )
            SERVER mssql_server OPTIONS (schema 'dbo', sql_query 'select [Product].[Id] as [id], CONCAT( Product.Name, CASE WHEN Product.Sku <> '''' THEN CONCAT('', '', Product.Sku) ELSE '''' END, CASE WHEN Product.ManufacturerName <> '''' THEN CONCAT('', '', Product.ManufacturerName) ELSE '''' END, CASE WHEN ProductSearch.EntityValue <> '''' THEN CONCAT('', '', ProductSearch.EntityValue) ELSE '''' END, CASE WHEN EXISTS ( SELECT 1 FROM Product_OEMCode_Mapping WHERE Product_OEMCode_Mapping.ProductId = Product.Id AND Product_OEMCode_Mapping.IsOEMCode = 1 ) THEN CONCAT('', '', ( SELECT STRING_AGG(Product_OEMCode_Mapping.OEMCodeDenormalized, '', '') FROM Product_OEMCode_Mapping WHERE Product_OEMCode_Mapping.ProductId = Product.Id )) ELSE '''' END ) AS search_text from [dbo].[Product] left join [B2B].[ProductSearch] on [Product].[Id] = [ProductSearch].[ProductId] where [Product].[Deleted] = 0 and [Product].[Published] = 1');");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP FOREIGN TABLE mssql_ProductSearchText');

        $userExists = DB::select(
            "SELECT * FROM pg_roles WHERE rolname='postgres'"
        );
        if (!empty($userExists)) {
            DB::statement(
                'DROP USER MAPPING FOR postgres SERVER mssql_server;'
            );
        }

        $userExists = DB::select(
            "SELECT * FROM pg_roles WHERE rolname='homestead'"
        );
        if (!empty($userExists)) {
            DB::statement(
                'DROP USER MAPPING FOR homestead SERVER mssql_server;'
            );
        }

        $userExists = DB::select(
            "SELECT * FROM pg_roles WHERE rolname='b2b_autostanic_hr_test'"
        );
        if (!empty($userExists)) {
            DB::statement(
                'DROP USER MAPPING FOR b2b_autostanic_hr_test SERVER mssql_server;'
            );
        }

        $userExists = DB::select(
            "SELECT * FROM pg_roles WHERE rolname='b2b_autostanic_hr_trial'"
        );

        if (!empty($userExists)) {
            DB::statement(
                'DROP USER MAPPING FOR b2b_autostanic_hr_trial SERVER mssql_server;'
            );
        }

        $userExists = DB::select(
            "SELECT * FROM pg_roles WHERE rolname='forge'"
        );

        if (!empty($userExists)) {
            DB::statement('DROP USER MAPPING FOR forge SERVER mssql_server;');
        }

        DB::statement('DROP SERVER mssql_server;');

        DB::statement('DROP EXTENSION odbc_fdw;');
    }
};
