<?php

use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\DecouverteController; // <-- N'oublie pas l'import du nouveau contrôleur !
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // Ajoute l'import en haut
use App\Http\Controllers\AdminController;

// ==========================================
// 🔓 ROUTES PUBLIQUES (Pas besoin de connexion)
// ==========================================
Route::get('/home-data', [HomeController::class, 'index']);
// Immobilier
Route::get('/annonces', [AnnonceController::class, 'index']);
Route::get('/annonces/{id}', [AnnonceController::class, 'show']);

// Découverte de la CI (Maquis, Artisans, etc.)
Route::get('/decouvertes', [DecouverteController::class, 'index']);
Route::get('/decouvertes/{id}', [DecouverteController::class, 'show']);

// Authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// 🔒 ROUTES PROTÉGÉES (Réservées aux connectés)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Gestion Immobilier
    Route::post('/annonces', [AnnonceController::class, 'store']);
    Route::get('/mes-annonces', [AnnonceController::class, 'myAnnonces']);
    Route::delete('/annonces/{id}', [AnnonceController::class, 'destroy']);
    
    // Gestion Découverte CI
    Route::post('/decouvertes', [DecouverteController::class, 'store']);
    Route::get('/mes-decouvertes', [DecouverteController::class, 'myDecouvertes']);
    Route::delete('/decouvertes/{id}', [DecouverteController::class, 'destroy']);
    
    // Déconnexion & Profil
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Nouvelles routes de modération
    Route::post('/admin/users/{id}/toggle-admin', [AdminController::class, 'toggleAdmin']);
    Route::post('/admin/users/{id}/toggle-active', [AdminController::class, 'toggleActive']);
    Route::post('/admin/annonces/{id}/valider', [AdminController::class, 'validerAnnonce']);
    Route::post('/admin/decouvertes/{id}/valider', [AdminController::class, 'validerDecouverte']);

    Route::get('/admin/dashboard', [AdminController::class, 'getDashboardData']);
    Route::delete('/admin/annonces/{id}', [AdminController::class, 'deleteAnnonce']);
    Route::delete('/admin/decouvertes/{id}', [AdminController::class, 'deleteDecouverte']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);

    // Routes pour modifier des publications
    Route::post('/annonces/{id}/update', [AnnonceController::class, 'update']);
    Route::post('/decouvertes/{id}/update', [DecouverteController::class, 'update']);
});