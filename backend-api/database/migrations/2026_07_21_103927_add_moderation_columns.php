<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. On ajoute un statut actif/inactif aux utilisateurs
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_admin');
        });

        // 2. On ajoute un statut de validation aux annonces immobilières
        Schema::table('annonces', function (Blueprint $table) {
            $table->boolean('est_valide')->default(false)->after('images');
        });

        // 3. On ajoute un statut de validation aux établissements (annuaire)
        Schema::table('decouvertes', function (Blueprint $table) {
            $table->boolean('est_valide')->default(false)->after('images');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
        Schema::table('annonces', function (Blueprint $table) {
            $table->dropColumn('est_valide');
        });
        Schema::table('decouvertes', function (Blueprint $table) {
            $table->dropColumn('est_valide');
        });
    }
};