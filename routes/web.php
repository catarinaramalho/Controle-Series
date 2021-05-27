<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ola', function () {
    return view ('public.index');  
});


Route::get('/series', 'App\Http\Controllers\SeriesController@index')
            ->name('listar_series')
            ->middleware('autenticador');
           
           
Route::get('/series/adicionar', 'App\Http\Controllers\SeriesController@create')
            ->name('form_criar_serie')
            ->middleware('autenticador');
           
Route::post('/series/adicionar', 'App\Http\Controllers\SeriesController@store');
          
Route::post('/series/remover/{id}', 'App\Http\Controllers\SeriesController@destroy');
Route::delete('/series/remover/{id}', 'App\Http\Controllers\SeriesController@destroy');
Route::get('/series/{serieId}/{temporadas}', 'App\Http\Controllers\TemporadasController@index');
Route::post('/series/{id}/editaNome', 'App\Http\Controllers\SeriesController@editaNome');
Route::get('/temporadas/{temporada}/episodios', 'App\Http\Controllers\EpisodiosController@index');
Route::post('/temporadas/{temporada}/episodios/assistir', 'App\Http\Controllers\EpisodiosController@assistir');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('/entrar', 'App\Http\Controllers\EntrarController@index');
Route::post('/entrar', 'App\Http\Controllers\EntrarController@entrar');
Route::get('/registrar', 'App\Http\Controllers\RegistroController@create');
Route::post('/registrar', 'App\Http\Controllers\RegistroController@store');

Route::get('/sair', function(){
    Auth::logout();
    return redirect('/entrar');
});
Route::get('/visualizando-email', function(){
    $email = new \App\Mail\NovaSerie(
        nome: 'Arrow',
        qtdTemporadas: 5,
        qtdEpisodios: 10
    );
    $user = (object) [
        'email' => 'catarina.ramalho@academico.ifpb.edu.br',
        'name' => 'Catarina Ramalho'
    ];
    \illuminate\Support\Facades\Mail::to($user)->send($email);
    return new App\Mail\NovaSerie(
        nome: 'Arrow',
        qtdTemporadas: 5,
        qtdEpisodios: 10
    );
});