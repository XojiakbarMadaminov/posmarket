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
        Schema::table('debtor_transactions', function (Blueprint $table) {
            $table->dropForeign(['debtor_id']);

            $table->foreign('debtor_id')
                ->references('id')->on('debtors')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debtor_transactions', function (Blueprint $table) {
            $table->dropForeign(['debtor_id']);

            $table->foreign('debtor_id')
                ->references('id')->on('debtors');
        });
    }
};
