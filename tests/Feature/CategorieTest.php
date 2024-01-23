<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Factories\AdminFactory;
use Database\Factories\CategorieFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategorieTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test : Ajout d'une nouvelle catégorie via l'API.
     *
     * Description :
     * Ce test vérifie que l'ajout d'une nouvelle catégorie à travers l'API fonctionne correctement.
     * L'administrateur authentifié doit pouvoir créer une nouvelle catégorie avec succès.
     *
     * Scénario :
     * 1. Créez un administrateur en utilisant la factory AdminFactory.
     * 2. Authentifiez-vous en tant qu'administrateur créé.
     * 3. Créez une nouvelle catégorie en utilisant la factory CategorieFactory.
     * 4. Effectuez une requête HTTP POST vers l'API pour ajouter la catégorie.
     * 5. Vérifiez que la requête a abouti en vérifiant le code de statut HTTP.
     * 6. Vérifiez que la réponse JSON contient le statut "OK" et le message "Categorie ajoutée avec succès".
     *
     * Prérequis :
     * - L'administrateur doit être authentifié avant d'exécuter ce test.
     */
    public function test_Ajouter_Categorie()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);

        $categorie = CategorieFactory::new()->make()->toArray();

        $response = $this->post('/api/categories', $categorie);
        $response->assertStatus(200)->json([
            'statut' => 'OK',
            'Message' => 'Categorie ajoutée avec succès'
        ]);
    }


    /**
     * Test : Récupération de la liste des catégories via l'API.
     *
     * Description :
     * Ce test vérifie que la récupération de la liste des catégories à travers l'API fonctionne correctement.
     * L'administrateur authentifié doit pouvoir obtenir la liste des catégories existantes.
     *
     * Scénario :
     * 1. Créez un administrateur en utilisant la factory AdminFactory.
     * 2. Authentifiez-vous en tant qu'administrateur créé.
     * 3. Créez cinq catégories en utilisant la factory CategorieFactory.
     * 4. Effectuez une requête HTTP GET vers l'API pour obtenir la liste des catégories.
     * 5. Vérifiez que la requête a abouti en vérifiant le code de statut HTTP.
     * 6. Vérifiez que la réponse JSON contient le statut "OK", le message "Liste des categories" et les données des catégories.
     *
     * Prérequis :
     * - L'administrateur doit être authentifié avant d'exécuter ce test.
     */
    public function test_lister_Categories()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);

        $categories = CategorieFactory::new()->count(5)->create();

        $response = $this->get('/api/categories');
        $response->assertStatus(200)->json([
            "statut" => "OK",
            "message" => "Liste des categories",
            'datas' => $categories
        ]);
    }



    /**
     * Test : Modification d'une catégorie via l'API.
     *
     * Description :
     * Ce test vérifie que la modification d'une catégorie à travers l'API fonctionne correctement.
     * L'administrateur authentifié doit pouvoir mettre à jour les détails d'une catégorie existante.
     *
     * Scénario :
     * 1. Créez un administrateur en utilisant la factory AdminFactory.
     * 2. Authentifiez-vous en tant qu'administrateur créé.
     * 3. Créez une catégorie en utilisant la factory CategorieFactory.
     * 4. Générez de nouvelles données pour la catégorie avec un libellé modifié.
     * 5. Effectuez une requête HTTP PUT vers l'API pour mettre à jour la catégorie avec les nouvelles données.
     * 6. Vérifiez que la requête a abouti en vérifiant le code de statut HTTP.
     * 7. Vérifiez que la réponse JSON contient le statut "OK" et le message "Categorie modifiée avec succès".
     *
     * Prérequis 
     * - L'administrateur doit être authentifié avant d'exécuter ce test.
     */
    public function test_Modifier_Categorie()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);

        $categorie = CategorieFactory::new()->create();

        $newCategorie = [
            'libelle' => 'nouveau libellé'
        ];

        $response = $this->put("/api/categories/{$categorie->id}", $newCategorie);
        $response->assertStatus(200)->json([
            'statut' => 'OK',
            'Message' => 'Categorie modifiée avec succès'
        ]);
    }


    /**
     * Test : Suppression d'une catégorie via l'API.
     *
     * Description :
     * Ce test vérifie que la suppression d'une catégorie à travers l'API fonctionne correctement.
     * L'administrateur authentifié doit pouvoir supprimer une catégorie existante.
     *
     * Scénario :
     * 1. Créez un administrateur en utilisant la factory AdminFactory.
     * 2. Authentifiez-vous en tant qu'administrateur créé.
     * 3. Créez une catégorie en utilisant la factory CategorieFactory.
     * 4. Effectuez une requête HTTP DELETE vers l'API pour supprimer la catégorie.
     * 5. Vérifiez que la requête a abouti en vérifiant le code de statut HTTP.
     * 6. Vérifiez que la réponse JSON contient le statut "OK" et le message "Categorie supprimée avec succès".
     *
     * Prérequis :
     * - L'administrateur doit être authentifié avant d'exécuter ce test.
     */
    public function test_Supprimer_Categorie()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);

        $categorie = CategorieFactory::new()->create();

        $response = $this->delete("/api/categories/{$categorie->id}");
        $response->assertStatus(200)->json([
            "statut" => "OK",
            "message" => "Categorie supprimée avec succès"
        ]);
    }
}
