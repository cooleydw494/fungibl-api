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
            $table->id();
            $table->integer('asa_id');
            $table->string('name', 60);
            $table->string('collection_id', 60);
            $table->boolean('in_pool')->default(1);
            $table->integer('submit_est_algo');
            $table->string('submit_algorand_address', 60);
            $table->integer('submit_iteration')->default(1);
            $table->integer('pull_est_algo')->nullable();
            $table->timestamp('pulled_at')->nullable();
            $table->string('pull_algorand_address', 60)->nullable();
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
