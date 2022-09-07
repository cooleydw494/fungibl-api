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
        Schema::create('pool_nfts', function (Blueprint $table) {
            $table->integer('asset_id')->primary();
            $table->string('name', 60);
            $table->string('unit_name', 60);
            $table->string('collection_name', 60);
            $table->string('creator_wallet', 60);
            $table->string('meta_standard', 10);
            $table->text('metadata')->nullable();
            $table->string('ipfs_image_url');
            $table->boolean('image_cached')->default(0)->nullable();
            $table->boolean('in_pool')->default(1);
            $table->integer('current_est_algo');
            $table->integer('submit_est_algo');
            $table->integer('submit_reward_fun');
            $table->string('submit_algorand_address', 60);
            $table->integer('submit_iteration')->default(1);
            $table->integer('pull_est_algo')->nullable();
            $table->integer('pull_cost_fun')->nullable();
            $table->string('pull_algorand_address', 60)->nullable();
            $table->timestamp('pulled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pool_nfts');
    }
};
