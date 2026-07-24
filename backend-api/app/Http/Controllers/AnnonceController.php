<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AnnonceController extends Controller
{
    // Récupérer et filtrer dynamiquement les annonces immobilières
    public function index(Request $request)
    {
        $query = Annonce::where('est_valide', true);

        // Filtre par commune
        if ($request->has('lieu') && !empty($request->lieu)) {
            $query->where('commune', 'like', '%' . $request->lieu . '%');
        }

        // Correspondance des onglets Angular ('louez', 'achetez', 'meuble')
        if ($request->has('type') && !empty($request->type)) {
            $type = $request->type;
            if ($type === 'louez') $query->where('type_contrat', 'location');
            if ($type === 'achetez') $query->where('type_contrat', 'vente');
            if ($type === 'meuble') $query->where('type_contrat', 'meuble');
        }

        // Filtre de budget maximum
        if ($request->has('prix') && $request->prix > 0) {
            $query->where('prix', '<=', $request->prix);
        }

        // Filtre par nombre de pièces (recherche textuelle sur le titre)
        if ($request->has('pieces') && !empty($request->pieces)) {
            $query->where('titre', 'like', '%' . $request->pieces . '%');
        }

        $annonces = $query->latest()->get();

        // Calcul dynamique des compteurs de statistiques globaux pour l'accueil
        $stats = [
            'en_location' => Annonce::where('type_contrat', 'location')->count(),
            'en_vente' => Annonce::where('type_contrat', 'vente')->count(),
            'vendeurs' => 27, 
            'visiteurs' => 1450
        ];

        // Agrégation par commune pour alimenter la liste latérale et la carte Leaflet
        $communesStats = Annonce::select('commune', DB::raw('count(*) as total_logements'))
            ->groupBy('commune')
            ->get()
            ->map(function($item) {
                return [
                    'commune' => $item->commune,
                    'ville' => 'Abidjan',
                    'total_logements' => $item->total_logements
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $annonces,
            'communes' => $communesStats,
            'stats' => $stats
        ]);
    }

 // Enregistrer l'annonce avec l'utilisateur connecté
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|numeric',
            'commune' => 'required|string',
            'type_contrat' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $imagesStructure = [];
        $categories = ['salon', 'chambre', 'cuisine', 'salle_de_bain', 'exterieur'];

        foreach ($categories as $cat) {
            $inputName = "images_$cat";
            if ($request->hasFile($inputName)) {
                $urls = [];
                foreach ($request->file($inputName) as $image) {
                    $path = $image->store("annonces/$cat", 'public');
                    $urls[] = asset('storage/' . $path);
                }
                $imagesStructure[$cat] = $urls;
            }
        }

        $annonce = Annonce::create([
            'user_id' => $request->user()->id, // <-- On associe l'ID de l'utilisateur connecté !
            'titre' => $request->titre,
            'description' => $request->description,
            'prix' => $request->prix,
            'commune' => $request->commune,
            'ville' => $request->input('ville', 'Abidjan'),
            'type_contrat' => $request->type_contrat,
            'images' => json_encode($imagesStructure),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Votre annonce a été publiée avec succès !',
            'data' => $annonce
        ], 201);
    }

    // Récupérer uniquement les annonces de l'utilisateur connecté
    public function myAnnonces(Request $request)
    {
        $annonces = Annonce::where('user_id', $request->user()->id)->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $annonces
        ]);
    }

    // Supprimer une annonce appartenant à l'utilisateur connecté
    public function destroy(Request $request, $id)
    {
        $annonce = Annonce::where('user_id', $request->user()->id)->find($id);

        if (!$annonce) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce introuvable ou vous n\'avez pas l\'autorisation de la supprimer.'
            ], 404);
        }

        $annonce->delete();

        return response()->json([
            'success' => true,
            'message' => 'L\'annonce a bien été supprimée.'
        ]);
    }
    // Afficher les détails d'une annonce spécifique
    public function show($id)
    {
        $annonce = Annonce::find($id);

        if (!$annonce) {
            return response()->json(['success' => false, 'message' => 'Annonce introuvable'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $annonce
        ]);
    }
    // (PROTÉGÉ) Modifier une annonce existante
    public function update(Request $request, $id)
    {
        // 1. On cherche l'annonce, et on vérifie qu'elle appartient bien à l'utilisateur connecté
        $annonce = Annonce::where('user_id', $request->user()->id)->find($id);

        if (!$annonce) {
            return response()->json(['success' => false, 'message' => 'Annonce introuvable ou accès refusé.'], 404);
        }

        // 2. On met à jour les champs textes
        $annonce->titre = $request->titre ?? $annonce->titre;
        $annonce->description = $request->description ?? $annonce->description;
        $annonce->prix = $request->prix ?? $annonce->prix;
        $annonce->type_contrat = $request->type_contrat ?? $annonce->type_contrat;
        $annonce->commune = $request->commune ?? $annonce->commune;
        $annonce->ville = $request->ville ?? $annonce->ville;

        // 3. Gestion des nouvelles images (Optionnel : s'il y a de nouvelles images, on écrase les anciennes)
        if ($request->hasFile('images')) {
            $urlsImages = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('annonces', 'public');
                $urlsImages[] = asset('storage/' . $path);
            }
            // Adapte ce code si tu as une structure d'images complexe (salon, cuisine, etc.)
            $annonce->images = json_encode(['exterieur' => $urlsImages]); 
        }

        // 4. LA RÈGLE D'OR : On repasse l'annonce en "Attente de validation"
        $annonce->est_valide = false;
        
        $annonce->save();

        return response()->json([
            'success' => true,
            'message' => 'Annonce modifiée avec succès. Elle est en attente de validation par un administrateur.',
            'data' => $annonce
        ]);
    }
}