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
        Schema::table('pool_nfts', function (Blueprint $table) {
            $table->dropPrimary('asset_id');
            $table->primary(['asset_id', 'submit_iteration']);
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
            $table->dropPrimary(['asset_id', 'submit_iteration']);
            $table->primary('asset_id');
        });
    }
};
