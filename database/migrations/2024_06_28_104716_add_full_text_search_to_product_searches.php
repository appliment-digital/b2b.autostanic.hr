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
        Schema::table('product_searches', function (Blueprint $table) {
            // Add a placeholder column for the tsvector
            $table->text('search_vector')->nullable();
        });

        // Alter the column to tsvector type
        DB::statement(
            'ALTER TABLE product_searches ALTER COLUMN search_vector TYPE tsvector USING to_tsvector(\'simple\', search_text)'
        );

        // Populate the tsvector column with existing data
        DB::statement('
            UPDATE product_searches
            SET search_vector = to_tsvector(\'simple\', search_text);
        ');

        // Create the trigger function to update search_vector
        DB::statement('
            CREATE FUNCTION product_searches_tsvector_trigger() RETURNS trigger AS $$
            begin
                new.search_vector :=
                    to_tsvector(\'simple\', new.search_text);
                return new;
            end
            $$ LANGUAGE plpgsql;
        ');

        // Create the trigger itself
        DB::statement('
            CREATE TRIGGER tsvectorupdate BEFORE INSERT OR UPDATE
            ON product_searches FOR EACH ROW EXECUTE FUNCTION product_searches_tsvector_trigger();
        ');

        // Create the GIN index on the tsvector column
        DB::statement('
            CREATE INDEX product_searches_search_vector_index
            ON product_searches USING GIN(search_vector);
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_searches', function (Blueprint $table) {
            DB::statement(
                'DROP TRIGGER IF EXISTS tsvectorupdate ON product_searches'
            );

            DB::statement(
                'DROP FUNCTION IF EXISTS product_searches_tsvector_trigger'
            );

            Schema::table('product_searches', function (Blueprint $table) {
                $table->dropColumn('search_vector');
            });
        });
    }
};
