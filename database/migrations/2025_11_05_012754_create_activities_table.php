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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            
            // Chave estrangeira para Opportunity
            $table->foreignId('opportunity_id')->constrained('opportunities');
            
            // Chave estrangeira para o Usuário que criou a atividade (User::class)
            $table->foreignId('user_id')->constrained('users');
            
            $table->string('titulo');
            $table->string('tipo'); // Ex: 'Visita', 'Ligação', 'Reunião'
            $table->timestamp('data_agendada');
            $table->text('descricao')->nullable();
            $table->string('status')->default('Pendente'); // 'Pendente', 'Concluída', 'Cancelada'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};