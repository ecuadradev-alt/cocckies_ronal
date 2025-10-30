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
            'data'    => News::orderBy('fecha_publicacion', 'desc')->get(),
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

        $news = News::create($validated);

        return response()->json([
            'message' => 'Noticia creada correctamente',
            'data'    => $news,
        ], 201);
    }

    // ✅ Mostrar una noticia
    public function show($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'Noticia no encontrada'], 404);
        }

        return response()->json([
            'message' => 'Detalle de noticia',
            'data'    => $news,
        ]);
    }

    // ✅ Actualizar una noticia
    public function update(Request $request, $id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json(['message' => 'Noticia no encontrada'], 404);
        }

        $validated = $request->validate([
            'titulo'            => 'sometimes|string|max:255',
            'descripcion'       => 'sometimes|string',
            'url'               => 'nullable|string|max:500',
            'fecha_publicacion' => 'nullable|date',
        ]);

        $news->update($validated);

        return response()->json([
            'message' => 'Noticia actualizada correctamente',
            'data'    => $news,
        ]);
    }

    // ✅ Eliminar una noticia
    public function destroy($id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json(['message' => 'Noticia no encontrada'], 404);
        }

        $news->delete();

        return response()->json(['message' => 'Noticia eliminada correctamente']);
    }
}
