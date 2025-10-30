<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // ✅ Listar todas las noticias
    public function index()
    {
        return response()->json([
            'message' => 'Lista de noticias',
            'data'    => Noticia::orderBy('fecha_publicacion', 'desc')->get(),
        ], 200);
    }

    // ✅ Crear una noticia
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'            => 'required|string|max:255',
            'descripcion'       => 'required|string',
            'url'               => 'nullable|string|max:500',
            'fecha_publicacion' => 'nullable|date',
        ]);

        $noticia = Noticia::create($validated);

        return response()->json([
            'message' => 'Noticia creada correctamente',
            'data'    => $noticia,
        ], 201);
    }

    // ✅ Mostrar una noticia
    public function show($id)
    {
        $noticia = Noticia::find($id);

        if (!$noticia) {
            return response()->json(['message' => 'Noticia no encontrada'], 404);
        }

        return response()->json([
            'message' => 'Detalle de noticia',
            'data'    => $noticia,
        ]);
    }

    // ✅ Actualizar una noticia
    public function update(Request $request, $id)
    {
        $noticia = Noticia::find($id);
        if (!$noticia) {
            return response()->json(['message' => 'Noticia no encontrada'], 404);
        }

        $validated = $request->validate([
            'titulo'            => 'sometimes|string|max:255',
            'descripcion'       => 'sometimes|string',
            'url'               => 'nullable|string|max:500',
            'fecha_publicacion' => 'nullable|date',
        ]);

        $noticia->update($validated);

        return response()->json([
            'message' => 'Noticia actualizada correctamente',
            'data'    => $noticia,
        ]);
    }

    // ✅ Eliminar una noticia
    public function destroy($id)
    {
        $noticia = Noticia::find($id);
        if (!$noticia) {
            return response()->json(['message' => 'Noticia no encontrada'], 404);
        }

        $noticia->delete();

        return response()->json(['message' => 'Noticia eliminada correctamente']);
    }
}
