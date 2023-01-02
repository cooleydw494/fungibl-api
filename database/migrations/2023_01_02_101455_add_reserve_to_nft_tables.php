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
            $table->string('reserve_wallet', 60)
                  ->after('creator_wallet');
            $table->unsignedSmallInteger('total_supply')
                  ->after('metadata');
            $table->unsignedSmallInteger('rarity_rank')
                  ->after('total_supply');
            $table->string('creator_name', 60)
                  ->after('collection_name');
        });
        Schema::table('pool_nfts', function (Blueprint $table) {
            $table->string('reserve_wallet', 60)
                  ->after('creator_wallet');
            $table->unsignedSmallInteger('total_supply')
                  ->after('metadata');
            $table->unsignedSmallInteger('rarity_rank')
                  ->after('total_supply');
            $table->string('creator_name', 60)
                  ->after('collection_name');
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
            $table->dropColumn('reserve_wallet');
            $table->dropColumn('total_supply');
            $table->dropColumn('rarity_rank');
            $table->dropColumn('creator_name');
        });
        Schema::table('pool_nfts', function (Blueprint $table) {
            $table->dropColumn('reserve_wallet');
            $table->dropColumn('total_supply');
            $table->dropColumn('rarity_rank');
            $table->dropColumn('creator_name');
        });
    }
};
