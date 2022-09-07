<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfts', function (Blueprint $table) {
            $table->smallInteger('cache_tries')
                  ->default(0)
                  ->after('image_cached');
        });
        Schema::table('pool_nfts', function (Blueprint $table) {
            $table->smallInteger('cache_tries')
                  ->default(0)
                  ->after('image_cached');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nfts', function (Blueprint $table) {
            $table->dropColumn('cache_tries');
        });
        Schema::table('pool_nfts', function (Blueprint $table) {
            $table->dropColumn('cache_tries');
        });
    }
};
