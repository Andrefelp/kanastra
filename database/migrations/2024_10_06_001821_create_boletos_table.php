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
        Schema::create('boletos', function (Blueprint $table) {
            $table->uuid()->primary()->index();
            $table->unsignedBigInteger('cliente_id');
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('data_envio_email')->nullable();

            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boletos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
        });

        Schema::dropIfExists('boletos');
    }
};
