<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Factories\AdminFactory;
use Database\Factories\LocaliteFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocaliteTest extends TestCase
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
     * Test : Ajout d'une nouvelle localité via l'API.
     *
     * Description :
     * Ce test vérifie que l'ajout d'une nouvelle Localité à travers l'API fonctionne correctement.
     * L'administrateur authentifié doit pouvoir créer une nouvelle localité avec succès.
     *
     * Scénario :
     * 1. Créez un administrateur en utilisant la factory AdminFactory.
     * 2. Authentifiez-vous en tant qu'administrateur créé.
     * 3. Créez une nouvelle Localite en utilisant la factory LocaliteFactory.
     * 4. Effectuez une requête HTTP POST vers l'API pour ajouter la catégorie.
     * 5. Vérifiez que la requête a abouti en vérifiant le code de statut HTTP.
     * 6. Vérifiez que la réponse JSON contient le statut "OK" et le message "Localité ajoutée avec succès".
     *
     * Prérequis :
     * - L'administrateur doit être authentifié avant d'exécuter ce test.
     */
    public function test_Ajouter_Localite()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);

        $localite = LocaliteFactory::new()->make()->toArray();

        $response = $this->post('/api/localites', $localite);
        $response->assertStatus(200)->json([
            'statut' => 'OK',
            'Message' => 'Localité ajoutée avec succès',
            'Role' => $localite
        ]);
    }


    /**
     * Test : Récupération de la liste des localités via l'API.
     *
     * Description :
     * Ce test vérifie que la récupération de la liste des localités à travers l'API fonctionne correctement.
     * L'administrateur authentifié doit pouvoir obtenir la liste des localités existantes.
     *
     * Scénario :
     * 1. Créez un administrateur en utilisant la factory AdminFactory.
     * 2. Authentifiez-vous en tant qu'administrateur créé.
     * 3. Créez cinq localités en utilisant la factory LocaliteFactory.
     * 4. Effectuez une requête HTTP GET vers l'API pour obtenir la liste des Localités.
     * 5. Vérifiez que la requête a abouti en vérifiant le code de statut HTTP.
     * 6. Vérifiez que la réponse JSON contient le statut "OK", le message "Liste des localités" et les données des catégories.
     *
     * Prérequis :
     * - L'administrateur doit être authentifié avant d'exécuter ce test.
     */
    public function test_lister_Localite()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);

        $localites = LocaliteFactory::new()->count(5)->create();

        $response = $this->get('/api/localites');
        $response->assertStatus(200)->json([
            "statut" => "OK",
            "message" => "Liste des Localités",
            'datas' => $localites
        ]);
    }



    /**
     * Test : Modification d'une Localité via l'API.
     *
     * Description :
     * Ce test vérifie que la modification d'une Localité à travers l'API fonctionne correctement.
     * L'administrateur authentifié doit pouvoir mettre à jour les détails d'une localité existante.
     *
     * Scénario :
     * 1. Créez un administrateur en utilisant la factory AdminFactory.
     * 2. Authentifiez-vous en tant qu'administrateur créé.
     * 3. Créez une localité en utilisant la factory LocaliteFactory.
     * 4. Générez de nouvelles données pour la localité avec un nom modifié.
     * 5. Effectuez une requête HTTP PUT vers l'API pour mettre à jour la localité avec les nouvelles données.
     * 6. Vérifiez que la requête a abouti en vérifiant le code de statut HTTP.
     * 7. Vérifiez que la réponse JSON contient le statut "OK" et le message "Localité modifiée avec succès".
     *
     * Prérequis 
     * - L'administrateur doit être authentifié avant d'exécuter ce test.
     */
    public function test_Modifier_Localite()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);

        $localite = LocaliteFactory::new()->create();

        $newlocalite = [
            'nom' => 'nouveau nom'
        ];

        $response = $this->put("/api/localites/{$localite->id}", $newlocalite);
        $response->assertStatus(200)->json([
            'statut' => 'OK',
            'Message' => 'Localité modifiée avec succès',
            'Localite' => $localite
        ]);
    }


    /**
     * Test : Suppression d'une localite via l'API.
     *
     * Description :
     * Ce test vérifie que la suppression d'une localité à travers l'API fonctionne correctement.
     * L'administrateur authentifié doit pouvoir supprimer une localité existante.
     *
     * Scénario :
     * 1. Créez un administrateur en utilisant la factory AdminFactory.
     * 2. Authentifiez-vous en tant qu'administrateur créé.
     * 3. Créez une localité en utilisant la factory LocaliteFactory.
     * 4. Effectuez une requête HTTP DELETE vers l'API pour supprimer la localité.
     * 5. Vérifiez que la requête a abouti en vérifiant le code de statut HTTP.
     * 6. Vérifiez que la réponse JSON contient le statut "OK" et le message "Localité supprimée avec succès".
     *
     * Prérequis :
     * - L'administrateur doit être authentifié avant d'exécuter ce test.
     */
    public function test_Supprimer_Localite()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);

        $localite = LocaliteFactory::new()->create();

        $response = $this->delete("/api/localites/{$localite->id}");
        $response->assertStatus(200)->json([
            "statut" => "OK",
            "message" => "Localite supprimée avec succès"
        ]);
    }
}
