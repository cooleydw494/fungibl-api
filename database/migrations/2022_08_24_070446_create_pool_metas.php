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
        Schema::create('pool_metas', function (Blueprint $table) {
            $table->string('key', 32)->unique('pool_metas_key');
            $table->string('value', 64);
        });
        // current_nft_count
        // current_nft_value
        // highest_nft_count
        // highest_nft_value
        // lowest_nft_count
        // lowest_nft_value
        // starting_nft_count
        // starting_nft_value
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pool_metas');
    }
};
