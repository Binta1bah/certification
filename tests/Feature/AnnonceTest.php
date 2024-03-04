<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Categorie;
use App\Models\Localite;
use App\Models\User;
use Database\Factories\AnnonceFactory;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Locale;

class AnnonceTest extends TestCase
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
     * Test : Ajout d'une annonce avec succès.
     *
     * Description :
     * Ce test vérifie que le processus d'ajout d'une annonce fonctionne correctement.
     * Un utilisateur authentifié doit pouvoir créer une nouvelle annonce en fournissant
     * toutes les informations nécessaires, y compris une ou plusieurs images.
     *
     * Scénario :
     * 1. Créez un utilisateur et connectez-vous.
     * 2. Générez un fichier d'image factice pour simuler le téléchargement d'une image.
     * 3. Créez des instances de catégorie et de localité.
     * 4. Préparez les données d'annonce avec toutes les informations nécessaires,
     *    y compris l'ID de la catégorie, l'ID de la localité et l'ID de l'utilisateur.
     * 5. Effectuez une requête HTTP POST pour ajouter une annonce avec les données préparées.
     * 6. Vérifiez que la requête a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     *
     */
    public function test_Ajouter_Annonce()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $image = UploadedFile::fake()->image('test.png');

        $categorie = Categorie::factory()->create();
        $localite = Localite::factory()->create();

        $annonce = [
            'libelle' => 'test_annonce',
            'description' => 'test_description',
            'etat' => 'Comme Neuf',
            'type' => 'Don',
            'categorie_id' => $categorie->id,
            'localite_id' => $localite->id,
            'date_limite' => '2024-01-24',
            'image' => [$image]
        ];

        $response = $this->post('/api/annonces', $annonce);
        $response->assertStatus(200)->json([
            'statut' => 'OK',
            'Message' => 'Annonce ajoutée avec succès',
        ]);
    }


    /**
     * Test : Affichage des détails d'une annonce avec succès.
     *
     * Description :
     * Ce test vérifie que l'affichage des détails d'une annonce fonctionne correctement.
     * Une annonce existante doit être accessible via une requête HTTP GET, et le code de statut
     * doit être 200 avec un message JSON contenant les détails de l'annonce.
     *
     * Scénario :
     * 1. Créez une annonce existante dans la base de données.
     * 2. Effectuez une requête HTTP GET pour récupérer les détails de l'annonce.
     * 3. Vérifiez que l'accès aux détails a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     *
     */
    public function test_Afficher_Details_Annonce()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $annonce = Annonce::factory()->create();
        $response = $this->get("api/detailsAnnonce/{$annonce->id}");
        $response->assertStatus(200)->json([
            "message" => "Les détails de l'annonce",
        ]);
    }

    /**
     * Test : Lister les annonces avec succès.
     *
     * Description :
     * Ce test vérifie que le processus de récupération de la liste des annonces fonctionne correctement.
     * Un utilisateur peut accéder à la liste des annonces via une requête HTTP GET.
     *
     * Scénario :
     * 1. Créez plusieurs annonces fictives dans la base de données.
     * 2. Effectuez une requête HTTP GET pour récupérer la liste des annonces.
     * 3. Vérifiez que la requête a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     * 4. Vérifiez que la réponse JSON contient les annonces fictives créées.
     *
     */
    public function test_Lister_Annonces()
    {
        $annonces = AnnonceFactory::new()->count(3)->create();
        $response = $this->get('/api/annonces');
        $response->assertStatus(200)->json([
            "statut" => "OK",
            "message" => "Liste des annonces",
            'datas' => $annonces
        ]);
    }


    /**
     * Test : Modification d'une annonce avec succès.
     *
     * Description :
     * Ce test vérifie que la modification d'une annonce fonctionne correctement.
     * L'utilisateur doit être authentifié, et la modification doit réussir avec un code de statut HTTP 200.
     *
     * Scénario :
     * 1. Créez un utilisateur et connectez-le.
     * 2. Créez une annonce existante dans la base de données.
     * 3. Préparez les nouvelles données pour la modification.
     * 4. Effectuez une requête HTTP PUT pour mettre à jour l'annonce avec les nouvelles données.
     * 5. Vérifiez que la modification a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     *
     */
    public function test_Modifier_Annonce()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $categorie = Categorie::factory()->create();
        $localite = Localite::factory()->create();

        $annonce = Annonce::factory()->create();

        $newAnnonce = [
            'libelle' => 'Nouvelle_Annonce',
            'description' => 'Nouvelle_description',
            'etat' => 'Bon Etat',
            'type' => 'Echange',
            'categorie_id' => $categorie->id,
            'localite_id' => $localite->id,
            'date_limite' => '2024-01-24',
        ];

        $response = $this->post("/api/annonces/{$annonce->id}", $newAnnonce);
        $response->assertStatus(200)->json([
            'statut' => 'OK',
            'Message' => 'Annonce mise à jour avec succès',
        ]);
    }

    /**
     * Test : Suppression d'une annonce avec succès.
     *
     * Description :
     * Ce test vérifie que la suppression d'une annonce fonctionne correctement.
     * Une annonce existante doit être supprimée via une requête HTTP DELETE, et le code de statut
     * doit être 200 avec un message JSON indiquant que l'annonce a été supprimée avec succès.
     *
     * Scénario :
     * 1. Créez un utilisateur pour simuler une authentification.
     * 2. Créez une annonce existante dans la base de données.
     * 3. Effectuez une requête HTTP DELETE pour supprimer l'annonce.
     * 4. Vérifiez que la suppression a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     *
     */
    public function test_Supprimer_Annonce()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $annonce = Annonce::factory()->create();
        $response = $this->delete("/api/annonces/{$annonce->id}");
        $response->assertStatus(200)->json([
            "statut" => "OK",
            "message" => "Annonce supprimée avec succès"
        ]);
    }
}
