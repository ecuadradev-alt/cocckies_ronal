<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    /**
     * Listar empresas
     */
    public function index()
    {
        $companies = Company::latest()->paginate(10);

        return response()->json($companies);
    }

    /**
     * Crear empresa
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'plan' => 'nullable|string|max:50'
        ]);

        $company = Company::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'plan' => $request->plan ?? 'free'
        ]);

        return response()->json([
            'message' => 'Empresa creada correctamente',
            'data' => $company
        ], 201);
    }

    /**
     * Mostrar empresa
     */
    public function show($id)
    {
        $company = Company::findOrFail($id);

        return response()->json($company);
    }

    /**
     * Actualizar empresa
     */
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'plan' => 'nullable|string|max:50'
        ]);

        $company->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'plan' => $request->plan ?? $company->plan
        ]);

        return response()->json([
            'message' => 'Empresa actualizada correctamente',
            'data' => $company
        ]);
    }

    /**
     * Eliminar empresa
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        $company->delete();

        return response()->json([
            'message' => 'Empresa eliminada correctamente'
        ]);
    }
}