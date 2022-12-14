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
        Schema::create('nfts', function (Blueprint $table) {
            $table->unsignedInteger('asset_id')->primary();
            $table->string('name', 60);
            $table->string('unit_name', 60);
            $table->string('collection_name', 60);
            $table->string('creator_wallet', 60);
            $table->string('meta_standard', 10);
            $table->text('metadata')->nullable();
            $table->string('ipfs_image_url');
            $table->boolean('image_cached')->default(0)->nullable();
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
        Schema::dropIfExists('nfts');
    }
};
