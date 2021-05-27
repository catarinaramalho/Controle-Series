<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeriesFormRequest;
use Illuminate\Http\Request;
use App\Serie;
use App\Models\{Temporada, Episodio};
use App\Services\CriadorDeSerie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SeriesController extends Controller
{
    /*
    public function __construct()
    
        $this->middleware('auth');
    }
    */

   public function index(Request $request){
        $series = Serie::query()->orderBy(column: 'name')->get();
        $mensagem = $request->session()->get(key: 'mensagem');
        return view ('series.index', ['series' => $series], 
        ['mensagem' => $mensagem]);

    }

    public function create(){
        return view ('series.create');
    }


    public function store(
        Request $request,
        CriadorDeSerie $criadorDeSerie
    ) {
        $serie = $criadorDeSerie->criarSerie(
            $request->nome,
            $request->qtd_temporadas,
            $request->qtd_episodios
        );

        $email = new \App\Mail\NovaSerie(
            $request->nome,
            $request->qtd_temporadas,
            $request->qtd_episodios
        );
        $email->subject = 'Nova Série Adicionada';
        $user = $request->user();
        \Illuminate\Support\Facades\Mail::to($user)->send($email);
        $request->session()
            ->flash(
                'mensagem',
                "Série {$serie->name} e suas {$request->qtd_temporadas} temporadas e {$request->qtd_episodios} episódios criados com sucesso {$serie->nome}"
            );
    
        return redirect()->route('listar_series');
    }

    public function destroy(Request $request)
    {
    
        $serie = Serie::find($request->id);
        $nomeSerie = $serie->name;
        $serie->temporadas->each(function (Temporada $temporada) {
            $temporada->episodios()->each(function(Episodio $episodio) {
                $episodio->delete();
            });
            $temporada->delete();
    
        });
        $serie->delete();
    
        Serie::destroy($request->id);
        $request->session()
            ->flash(
                'mensagem',
                "Série $nomeSerie removida com sucesso"
            );
        return redirect()->route('listar_series');
    }

    public function editaNome(int $id, Request $request)
    {
        $novoNome = $request->name;
        $serie = Serie::find($id);
        $serie->name = $novoNome;
        $serie->save();
    }


}