<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/salvar-local', function (Request $request) {
    $login = $request->input('login');

    // URL da API do GitHub para obter informações do usuário
    $apiUrl = "https://api.github.com/users/$login";

    $client = new Client();

    try {
        // Faz uma requisição GET para a API do GitHub
        $response = $client->get($apiUrl);

        if ($response->getStatusCode() === 200) {
            $userDataJson = $response->getBody()->getContents();

            $filePath = 'data/user.json';

            if (!is_dir('data')) {
                mkdir('data', 0755, true);
            }

            // Se o arquivo já existir, adiciona os dados ao final dele
            if (file_exists($filePath)) {
                $existingData = file_get_contents($filePath);
                $existingArray = json_decode($existingData, true);
                $existingArray[] = json_decode($userDataJson, true);
                $updatedData = json_encode($existingArray);

                file_put_contents($filePath, $updatedData);
            } else {
                file_put_contents($filePath, "[$userDataJson]");
            }

            return response()->json(['message' => 'Dados do usuário salvos com sucesso']);
        } else {
            return response()->json(['error' => 'Erro ao obter informações do usuário'], 500);
        }
    } catch (RequestException $e) {
        return response()->json(['error' => 'Erro ao fazer requisição para a API do GitHub'], 500);
    }
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::apiresource('tags', 'TagController');
    Route::apiresource('ptypes', 'PtypeController');
});
