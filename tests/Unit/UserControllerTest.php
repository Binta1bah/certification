<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\api\UserController;

class UserControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_Inscription()
    {
        // Créer une instance de la classe UserController ou de la classe où se trouve la méthode register
        $userController = new UserController(); // Assurez-vous d'importer la classe UserController si nécessaire

        $validationDatas = new Request();
        // Créer une instance de Request simulée avec les données de l'utilisateur
        $validationDatas->merge([
            'name' => 'John Doe',
            'email' => 'johna@example.com',
            'password' => 'password123',
            'telephone' => '772345678',
            'photo' => UploadedFile::fake()->image('avatar.jpg')
        ]);

        // Appeler la méthode register avec la Request simulée
        $response = $userController->register($validationDatas);

        // Vérifier si la réponse est conforme
        $this->assertEquals(200, $response->status()); // Vérifier le code de statut HTTP

        $responseData = $response->getData(); // Obtenir les données de la réponse JSON
        $this->assertEquals('ok', $responseData->status); // Vérifier le statut
        $this->assertEquals('Inscription effectuée avec succes', $responseData->message); // Vérifier le message de réussite
        // Vous pouvez ajouter d'autres assertions pour vérifier les données de l'utilisateur retournées si nécessaire
    }

}
