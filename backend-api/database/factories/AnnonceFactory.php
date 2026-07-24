<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AnnonceFactory extends Factory
{
    public function definition(): array
    {
        // Les communes que ta carte Leaflet sait placer
        $communes = ['Cocody', 'Yopougon', 'Marcory', 'Commerce'];
        $communeChoisie = $this->faker->randomElement($communes);
        
        // Si c'est Commerce, c'est à Bouaké, sinon c're à Abidjan
        $ville = ($communeChoisie === 'Commerce') ? 'Bouaké' : 'Abidjan';

        $types = ['Superbe Villa', 'Appartement de luxe', 'Studio moderne', 'Duplex', 'Maison familiale','Résidence meublée VIP', 'Appartement meublé'     ];
        $titre = $this->faker->randomElement($types) . ' - ' . $this->faker->words(3, true);

        return [
            'titre' => ucfirst($titre),
            'description' => $this->faker->paragraph(3),
            // Génère un prix réaliste entre 80 000 et 800 000 FCFA
            'prix' => $this->faker->numberBetween(8, 80) * 10000, 
            'commune' => $communeChoisie,
            'ville' => $ville,
        ];
    }
}