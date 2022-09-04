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
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('algorand_address', 60)->unique('users_algorand_address');
            $table->string('username', 32)->nullable()->unique('users_username');
            $table->string('nfd', 32)->unique('users_nfd')->nullable();
            $table->integer('favorite_nft')->unique('users_favorite_nft')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('nonce', 16);

            $table->unsignedSmallInteger('submission_count')->default(0);
            $table->unsignedSmallInteger('submission_count_mtd')->default(0);
            $table->unsignedSmallInteger('submission_count_ytd')->default(0);
            $table->unsignedSmallInteger('pull_count')->default(0);
            $table->unsignedSmallInteger('pull_count_mtd')->default(0);
            $table->unsignedSmallInteger('pull_count_ytd')->default(0);
            $table->unsignedSmallInteger('submission_est_algo')->default(0);
            $table->unsignedSmallInteger('submission_est_algo_mtd')->default(0);
            $table->unsignedSmallInteger('submission_est_algo_ytd')->default(0);
            $table->unsignedSmallInteger('pull_est_algo')->default(0);
            $table->unsignedSmallInteger('pull_est_algo_mtd')->default(0);
            $table->unsignedSmallInteger('pull_est_algo_ytd')->default(0);
            $table->unsignedSmallInteger('fun_rewarded')->default(0);
            $table->unsignedSmallInteger('fun_rewarded_mtd')->default(0);
            $table->unsignedSmallInteger('fun_rewarded_ytd')->default(0);
            $table->unsignedSmallInteger('fun_spent')->default(0);
            $table->unsignedSmallInteger('fun_spent_mtd')->default(0);
            $table->unsignedSmallInteger('fun_spent_ytd')->default(0);
            $table->unsignedSmallInteger('fees_paid_algo')->default(0);
            $table->unsignedSmallInteger('fees_paid_algo_mtd')->default(0);
            $table->unsignedSmallInteger('fees_paid_algo_ytd')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
//        Schema::dropIfExists('users');
    }
};
