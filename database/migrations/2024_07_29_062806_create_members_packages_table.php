<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members_packages', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->integer('package_id');
            $table->integer('transaction_id');
            $table->string('amount');
            $table->string('subcribe_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members_packages');
    }
};
