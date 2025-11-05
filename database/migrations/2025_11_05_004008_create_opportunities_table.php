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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();

            // Relacionamento com o Corretor (User) - Chave estrangeira
            $table->foreignId('corretor_id')->constrained('users');

            // DADOS CHAVE DO CARD (Fase 1: Lead)
            $table->string('nome_lead');
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('fonte_lead')->nullable();
            
            // Coluna principal para o Kanban
            $table->string('etapa_pipeline')->default('Lead');

            $table->string('preferencia_imovel')->nullable();
            $table->decimal('valor_max_compra', 10, 2)->nullable(); // 10 dígitos no total, 2 decimais

            // DADOS CONTRATUAIS (Fase 2: Cliente Qualificado - Opcionais)
            $table->string('documento_numero')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('profissao')->nullable();
            $table->text('endereco_cliente')->nullable();

            // Colunas de SoftDeletes e Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};