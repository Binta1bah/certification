<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Article;
use App\Models\Commentaire;
use Database\Factories\AdminFactory;
use Database\Factories\ArticleFactory;
use Database\Factories\CommentaireFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleTest extends TestCase
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
     * Test : Ajout d'un article avec succès.
     *
     * Description :
     * Ce test vérifie que le processus d'ajout d'un article fonctionne correctement.
     * Un administrateur authentifié doit pouvoir ajouter un nouvel article avec succès.
     *
     * Scénario :
     * 1. Créez un administrateur factice et connectez-vous.
     * 2. Générez des données factices pour un nouvel article à ajouter.
     * 3. Effectuez une requête HTTP POST pour ajouter l'article avec les données générées.
     * 4. Vérifiez que la requête a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     * 5. Vérifiez que la réponse JSON contient les informations de l'article ajouté.
     *
     */
    public function test_Ajouter_Article()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);
        $article = ArticleFactory::new()->make()->toArray();

        $response =  $this->post('/api/articles', $article);
        $response->assertStatus(200)->json([
            'statut' => 'OK',
            'Message' => 'Article ajouté avec succès',
            'Article' => $article
        ]);
    }


    /**
     * Test : Lister les articles.
     *
     * Description :
     * Ce test vérifie la fonctionnalité de récupération de la liste des articles via une requête HTTP GET.
     *
     * Scénario :
     * 1. Générez plusieurs articles factices dans la base de données.
     * 2. Effectuez une requête HTTP GET pour récupérer la liste des articles.
     * 3. Vérifiez que la requête a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     * 4. Vérifiez que la réponse JSON indique le statut "OK" et le message "Liste des articles".
     *
     */
    public function test_Lister_Articles()
    {
        $article = ArticleFactory::new()->count(3)->create();
        $response = $this->get('/api/articles');
        $response->assertStatus(200)->json([
            "statut" => "OK",
            "message" => "Liste des articles",
        ]);
    }



    /**
     * Test : Affichage des détails d'un article.
     *
     * Description :
     * Ce test vérifie que le processus d'affichage des détails d'un article fonctionne correctement.
     * Un utilisateur doit pouvoir récupérer les informations détaillées d'un article existant.
     *
     * Scénario :
     * 1. Générez un article factice dans la base de données.
     * 2. Effectuez une requête HTTP GET pour récupérer les détails de l'article généré.
     * 3. Vérifiez que la requête a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     * 4. Vérifiez que la réponse JSON contient les détails de l'article récupéré.
     */
    public function test_Afficher_Details_Article()
    {
        $article = Article::factory()->create();

        $response =  $this->get("/api/articles/{$article->id}");
        $response->assertStatus(200)->json([
            "message" => "Les détails de l'articles",
            "data" => $article
        ]);
    }



    /**
     * Test : Modification d'un article.
     *
     * Description :
     * Ce test vérifie que le processus de modification d'un article fonctionne correctement.
     * Un administrateur doit pouvoir modifier un article existant.
     *
     * Scénario :
     * 1. Créez un administrateur factice et authentifiez-le.
     * 2. Générez un article factice dans la base de données.
     * 3. Effectuez une requête HTTP PUT pour modifier l'article généré.
     * 4. Vérifiez que la requête a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     * 5. Vérifiez que la réponse JSON indique que l'article a été modifié avec succès.
     */
    public function test_Modifier_Article()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);
        $article = Article::factory()->create();

        $newArticle = [
            'libelle' => 'new libelle',
            'contenu' => 'new contenu'
        ];

        $response = $this->post("/api/articles/{$article->id}", $newArticle);
        $response->assertStatus(200)->json([
            'statut' => 'OK',
            'Message' => 'Article modifié avec succès',
        ]);
    }



    /**
     * Test : Supprimer un article.
     *
     * Description :
     * Ce test vérifie la fonctionnalité de suppression d'un article via une requête HTTP DELETE.
     *
     * Scénario :
     * 1. Créez un administrateur factice et connectez-vous en tant qu'administrateur.
     * 2. Générez un article factice dans la base de données.
     * 3. Effectuez une requête HTTP DELETE pour supprimer l'article créé.
     * 4. Vérifiez que la requête a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     * 5. Vérifiez que la réponse JSON indique le statut "OK" et le message "Article supprimé avec succès".
     *
     * Prérequis :
     * - Un administrateur doit être créé au préalable.
     */
    public function test_Supprimer_Article()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin);
        $article = Article::factory()->create();
        $response = $this->delete("/api/articles/{$article->id}");
        $response->assertStatus(200)->json([
            "statut" => "OK",
            "message" => "Article supprimé avec succès"
        ]);
    }



    /**
     * Test : Commenter un article.
     *
     * Description :
     * Ce test vérifie la fonctionnalité de commentaire sur un article.
     * Un utilisateur authentifié doit pouvoir ajouter un commentaire à un article existant.
     *
     * Scénario :
     * 1. Créer un utilisateur.
     * 2. Authentifier l'utilisateur.
     * 3. Créer un article existant.
     * 4. Effectuer une requête HTTP POST pour ajouter un commentaire à l'article.
     * 5. Vérifier que la requête a réussi en vérifiant le code de statut HTTP.
     * 6. Vérifier que la réponse JSON contient le statut 'OK' et les détails du commentaire ajouté.
     *
     */
    public function test_Commenter_Article()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $article = ArticleFactory::new()->create();
        $commentaire = [
            'commentaire' => 'test_commentaire',
            'article_id' => $article,
            'user_id' => $user
        ];
        $response = $this->post("/api/commentaires/{$article->id}", $commentaire);
        $response->assertStatus(200)->json([
            'Statut' => 'OK',
            'Commentaire' => $commentaire
        ]);
    }


    /**
     * Test : Lister les commentaires d'un article.
     *
     * Description :
     * Ce test vérifie la fonctionnalité de lister les commentaires associés à un article.
     * Un utilisateur authentifié doit pouvoir récupérer la liste des commentaires d'un article spécifique.
     *
     * Scénario :
     * 1. Créer un utilisateur.
     * 2. Authentifier l'utilisateur.
     * 3. Créer un article existant.
     * 4. Créer quelques commentaires associés à l'article.
     * 5. Effectuer une requête HTTP GET pour récupérer la liste des commentaires de l'article.
     * 6. Vérifier que la requête a réussi en vérifiant le code de statut HTTP.
     * 7. Vérifier que la réponse JSON contient le statut 'OK' et les détails des commentaires associés à l'article.
     *
     */
    public function test_lister_Commentaires_Articles()
    {

        $user = User::factory()->create();

        $this->actingAs($user);

        $article = Article::factory()->create();

        // Créer quelques commentaires associés à l'article
        $commentaires = Commentaire::factory()->count(3)->create([
            'commentaire' => 'test_liste_commentaire',
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);

        $response = $this->get("/api/commentaires/{$article->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'liste des commentaires',
            'Commentaires' => $commentaires->toArray(),
        ]);
    }
}
