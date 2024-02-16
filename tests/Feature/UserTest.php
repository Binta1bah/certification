<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tymon\JWTAuth\Facades\JWTAuth;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
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
     * Test : Inscription d'un utilisateur via l'API.
     *
     * Description :
     * Ce test vérifie que le processus d'inscription d'un utilisateur via l'API fonctionne correctement.
     * L'utilisateur doit pouvoir s'inscrire avec succès en fournissant les informations requises.
     *
     * Scénario :
     * 1. Créez un fichier image factice à l'aide de la méthode UploadedFile::fake().
     * 2. Préparez les données utilisateur avec un nom, un email, un mot de passe, un numéro de téléphone et une photo factice.
     * 3. Effectuez une requête HTTP POST vers l'API pour inscrire l'utilisateur en utilisant les données préparées.
     * 4. Vérifiez que la requête a abouti en vérifiant le code de statut HTTP.
     * 5. Vérifiez que la réponse JSON contient le statut "OK" et le message "Inscription effectuée avec succès".
     *
     * Prérequis :
     * - Aucun utilisateur ne doit être authentifié avant d'exécuter ce test.
     */
    public function test_Inscription()
    {
        $photo = UploadedFile::fake()->image('test.png');
        $user = [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'telephone' => '777474747',
            'photo' => $photo

        ];
        $response = $this->post('/api/inscription', $user);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Inscription effectuée avec succes',
            ]);
    }

    /**
     * Test : Authentification d'un utilisateur via l'API.
     *
     * Description :
     * Ce test vérifie que le processus d'authentification d'un utilisateur via l'API fonctionne correctement.
     * L'utilisateur doit pouvoir s'authentifier avec succès en fournissant des identifiants valides.
     *
     * Scénario :
     * 1. Créez un fichier image factice à l'aide de la méthode UploadedFile::fake().
     * 2. Créez un utilisateur avec un nom, un numéro de téléphone, un email, un mot de passe (hashé) et une photo factice.
     * 3. Effectuez une requête HTTP POST vers l'API pour vous authentifier en utilisant les identifiants de l'utilisateur.
     * 4. Vérifiez que la connexion a réussi en vérifiant le code de statut HTTP.
     * 5. Vérifiez que l'utilisateur est correctement authentifié en utilisant la méthode assertAuthenticatedAs().
     *
     * Prérequis :
     * - Aucun utilisateur ne doit être authentifié avant d'exécuter ce test.
     */
    public function test_Authentification()
    {
        $photo = UploadedFile::fake()->image('test.png');
        $password = 'password';
        $user = User::create([
            'name' => 'user',
            'telephone' => '778547874',
            'email' => 'user@gmail.com',
            'password' => Hash::make($password),
            'photo' => $photo,
            'role_id' => 1
        ]);

        // Effectuer une requête HTTP POST pour s'authentifier
        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // Vérifier que la connexion a réussi
        $response->assertStatus(200);
        // Vérifier que l'utilisateur est connecté
        $this->assertAuthenticatedAs($user, 'api');
    }

    /**
     * Test : Modification du profil utilisateur via l'API.
     *
     * Description :
     * Ce test vérifie que l'utilisateur peut modifier son profil avec succès en utilisant l'API.
     * L'utilisateur doit pouvoir mettre à jour des informations telles que le nom, l'email, le mot de passe, le numéro de téléphone
     * et la photo de profil en fournissant des données valides.
     *
     * Scénario :
     * 1. Créez un utilisateur factice à l'aide de la factory User.
     * 2. Connectez-vous en tant qu'utilisateur en utilisant la méthode actingAs().
     * 3. Créez une nouvelle photo factice avec UploadedFile::fake().
     * 4. Définissez les nouvelles données de l'utilisateur, y compris le nom, l'email, le mot de passe, le numéro de téléphone
     *    et la nouvelle photo.
     * 5. Effectuez une requête HTTP PUT vers l'API pour mettre à jour le profil avec les nouvelles données.
     * 6. Vérifiez que la modification du profil a réussi en vérifiant le code de statut HTTP et le message JSON retourné.
     * 7. Rafraîchissez l'utilisateur dans la base de données pour obtenir les données mises à jour.
     * 8. Facultatif : Ajoutez des assertions supplémentaires pour vérifier les champs spécifiques mis à jour dans la base de données.
     *
     */
    public function test_ModifierProfil()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $newPhoto = UploadedFile::fake()->image('new_photo.png');
        $newdata = [
            'name' => 'test1',
            'email' => 'test1@gmail.com',
            'password' => 'password',
            'telephone' => '770140404',
            'photo' => $newPhoto
        ];
        $response = $this->post('/api/update', $newdata);
        $response->assertStatus(200)->assertJson([
            "statut" => "ok",
            "message" => "Modification effectuée",
        ]);
        // Rafrechir l'utilisateur dans la base de donnée
        $user = $user->fresh();
    }


    /**
     * Test : Déconnexion d'un utilisateur via l'API.
     *
     * Description :
     * Ce test vérifie que le processus de déconnexion d'un utilisateur via l'API fonctionne correctement.
     * L'utilisateur doit pouvoir se déconnecter avec succès après s'être authentifié.
     *
     * Scénario :
     * 1. Créez un utilisateur à l'aide de la factory.
     * 2. Connectez-vous en tant qu'utilisateur.
     * 3. Générez un token JWT pour l'utilisateur.
     * 4. Effectuez une requête HTTP GET vers l'API de déconnexion en incluant le token JWT dans l'en-tête de la requête.
     * 5. Vérifiez que la déconnexion a réussi en vérifiant le code de statut HTTP et le message de la réponse.
     *
     * Prérequis :
     * - Le token JWT doit être généré et inclus dans l'en-tête de la requête.
     */
    public function test_Deconnexion()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $token = JWTAuth::fromUser($user); // Générer le token JWT pour l'utilisateur

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token]) // Inclure le token dans l'en-tête
            ->get('/api/logout');
        $response->assertStatus(200)->assertJson([
            'message' => 'Déconnexion effectuée',
        ]);
    }
}
