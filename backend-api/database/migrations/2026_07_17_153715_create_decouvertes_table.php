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
        Schema::create('decouvertes', function (Blueprint $table) {
            $table->id();
            
            // L'utilisateur qui a publié (pour qu'il puisse gérer/supprimer sa publication)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('nom'); // Ex: Maquis Le Dromadaire, Plomberie Express
            $table->string('categorie'); // Ex: restaurant, bar, artisan, service
            $table->text('description');
            $table->string('commune'); // Ex: Yopougon, Marcory
            $table->string('telephone'); // Important pour contacter l'artisan ou le resto
            $table->text('images')->nullable(); // Galerie photo
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decouvertes');
    }
};
