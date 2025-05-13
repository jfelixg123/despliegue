<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class JugadorController extends Controller
{

public function getJugadores()
{
    try {
        $jugadores = Jugador::with(['usuario', 'rol', 'entrenador'])
            ->get();
        return response()->json($jugadores);
    } catch (\Exception $e) {
        return response()->json(['error' => 'No se pudieron obtener los jugadores'], 500);
    }
}

public function contratarJugador(Request $request)
{
    try {
        $validated = $request->validate([
            'jugador_id' => 'required|exists:jugadores,id_jugador',
            'entrenador_id' => 'required|exists:entrenadores,id_entrenador',
        ]);

        $jugador = Jugador::findOrFail($validated['jugador_id']);

        // Verificar si el jugador ya tiene entrenador
        if ($jugador->id_entrenador) {
            return response()->json([
                'message' => 'Este jugador ya pertenece a un equipo'
            ], 400);
        }

        // Asignar el entrenador al jugador
        $jugador->id_entrenador = $validated['entrenador_id'];
        $jugador->save();

        return response()->json([
            'message' => 'Jugador contratado exitosamente',
            'jugador' => $jugador
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Error al contratar jugador: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error al contratar jugador',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function showNewJugador()
    {
        return view('usuarios.newJugador');
    }

    public function newJugador(Request $request)
    {
    try {

        $usuario = Auth::user();
        if (!$usuario) {
            throw new \Exception('Usuario no autenticado');
        }

        $validated = $request->validate([
            'rango' => 'required|string|max:255',
            'palmares' => 'required|string|max:255',
            'nombreJugador' => 'required|string|max:255',
            'tagJugador' => 'required|string|max:255',
            'rol' => 'required|integer|exists:roles,id_rol',
        ]);

        $jugador = Jugador::find($usuario->id_usuario);

        if ($jugador) {
            // Actualizar jugador existente
            $jugador->update([
                'rango_valorant' => $validated['rango'],
                'palmares' => $validated['palmares'],
                'kills' => 0,
                'deaths' => 0,
                'assists' => 0,
                'id_rol' => $validated['rol']
            ]);

        } else {
            $jugador = Jugador::create([
                'id_jugador' => $usuario->id_usuario,
                'rango_valorant' => $validated['rango'],
                'palmares' => $validated['palmares'],
                'kills' => 0,
                'deaths' => 0,
                'assists' => 0,
                'id_rol' => $validated['rol'],
            ]);
        }

        $usuario->update([
            'tipo_usuario' => 'jugador',
            'nombre_jugador' => $validated['nombreJugador'],
            'tag_jugador' => $validated['tagJugador'],
        ]);

        return redirect()->route('landing')
                        ->with('success', '¡Te has convertido en jugador exitosamente!');

    } catch (\Exception $e) {
        \Log::error('Error al crear el jugador: ' . $e->getMessage());
        return back()
            ->withErrors(['error' => 'Error al crear el jugador: ' . $e->getMessage()])
            ->withInput();
    }
}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jugadores = Jugador::with(['usuario', 'rol'])->get();
        return view('usuarios.jugadoresGeneral', compact('jugadores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Jugador $jugador)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jugador $jugador)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jugador $jugador)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jugador $jugador)
    {
        //
    }
}
