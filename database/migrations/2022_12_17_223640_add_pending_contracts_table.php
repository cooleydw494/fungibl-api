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
        Schema::create('pending_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nft_asset_id');
            $table->unsignedBigInteger('user_id');
            $table->string('contract_info', 90);
            $table->timestamps();
            $table->softDeletes();
        });

        // also change contract_info in pool_nfts to string
        Schema::table('pool_nfts', function (Blueprint $table) {
            $table->string('contract_info', 90)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_contracts');
    }
};
