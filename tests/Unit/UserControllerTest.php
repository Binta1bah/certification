<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

use Illuminate\Http\UploadedFile;
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

    public function testUserRegistrationSuccess()
    {
        // Créer une instance de la classe UserController ou de la classe où se trouve la méthode register
    $userController = new UserController(); // Assurez-vous d'importer la classe UserController si nécessaire

    // Simuler la validation en appelant la méthode validate directement sur le contrôleur
    $validatedData = $userController->validate(request(), [
        'name' => 'required|string|max:255|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ -]+$/',
        'email' => 'required|unique:users,email|regex:/^[a-zA-Z0-9]+@[a-z]+\.[a-z]{2,}$/',
        'password' => 'required|string|min:8',
        'photo' => 'required',
        'telephone' => 'required|string|max:9|regex:/^7[0-9]{8}$/|unique:users,telephone',
    ]);

    // Créer une instance de Request simulée avec les données de l'utilisateur
    $request = new Request([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'telephone' => '712345678',
        'photo' => UploadedFile::fake()->image('avatar.jpg')
    ]);

    // Appeler la méthode register avec la Request simulée
    $response = $userController->register($request);

    // Vérifier si la réponse est conforme
    $this->assertEquals(200, $response->status()); // Vérifier le code de statut HTTP

    $responseData = $response->getData(); // Obtenir les données de la réponse JSON
    $this->assertEquals('ok', $responseData->status); // Vérifier le statut
    $this->assertEquals('Inscription effectuée avec succes', $responseData->message); // Vérifier le message de réussite
    // Vous pouvez ajouter d'autres assertions pour vérifier les données de l'utilisateur retournées si nécessaire
    }
}
