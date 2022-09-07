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
            $table->unsignedInteger('current_nft_count');
            $table->unsignedInteger('current_pool_value');
            $table->unsignedInteger('current_avg_reward');
            $table->unsignedInteger('current_pull_cost');

            $table->unsignedInteger('starting_nft_count');
            $table->unsignedInteger('starting_pool_value');
            $table->unsignedInteger('starting_avg_reward');
            $table->unsignedInteger('starting_pull_cost');

            $table->unsignedInteger('lowest_nft_count');
            $table->unsignedInteger('lowest_pool_value');
//            $table->unsignedInteger('lowest_nft_value');
            $table->unsignedInteger('lowest_avg_reward');
            $table->unsignedInteger('lowest_pull_cost');

            $table->unsignedInteger('highest_nft_count');
            $table->unsignedInteger('highest_pool_value');
//            $table->unsignedInteger('highest_nft_value');
            $table->unsignedInteger('highest_avg_reward');
            $table->unsignedInteger('highest_pull_cost');

            $table->unsignedInteger('app_supply_fun');
            $table->unsignedInteger('circulating_supply_fun');
            $table->unsignedInteger('llc_supply_fun');
            $table->unsignedInteger('beta_supply_fun');
            $table->unsignedInteger('public_supply_fun');

            $table->timestamps();
        });

        \App\Models\PoolMeta::create([
            'current_nft_count' => 0,
            'current_pool_value' => 0,
            'current_avg_reward' => 0,
            'current_pull_cost' => 0,

            'starting_nft_count' => 0,
            'starting_pool_value' => 0,
            'starting_avg_reward' => 0,
            'starting_pull_cost' => 0,

            'lowest_nft_count' => 0,
            'lowest_pool_value' => 0,
//            'lowest_nft_value' => 0,
            'lowest_avg_reward' => 0,
            'lowest_pull_cost' => 0,

            'highest_nft_count' => 0,
            'highest_pool_value' => 0,
//            'highest_nft_value' => 0,
            'highest_avg_reward' => 0,
            'highest_pull_cost' => 0,

            'app_supply_fun' => 750000000,
            'circulating_supply_fun' => 250000000,
            'llc_supply_fun' => 200000000,
            'beta_supply_fun' => 50000000,
            'public_supply_fun' => 0,
        ]);
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
