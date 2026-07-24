<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
    {
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            
            // Relie l'annonce à un utilisateur (nullable pour ne pas casser tes anciennes données de test)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->string('titre');
            $table->text('description');
            $table->integer('prix');
            $table->string('commune');
            $table->string('ville')->default('Abidjan');
            $table->string('type_contrat')->default('location');
            $table->text('images')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};
