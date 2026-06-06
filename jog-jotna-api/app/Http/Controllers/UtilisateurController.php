<?php
namespace App\Http\Controllers;
use App\Models\Utilisateur;
use Illuminate\Http\{Request,JsonResponse};

class UtilisateurController extends Controller {
    private function adminOnly(Request $request): ?JsonResponse {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Accès réservé aux administrateurs.'], 403);
        }
        return null;
    }

    public function index(Request $request): JsonResponse {
        if ($r = $this->adminOnly($request)) return $r;
        $q = Utilisateur::query()
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->search, fn($q) => $q->where(fn($q) => $q
                ->where('nom', 'like', "%{$request->search}%")
                ->orWhere('prenom', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")))
            ->orderBy('nom');
        return response()->json($q->paginate(20));
    }

    public function store(Request $request): JsonResponse {
        if ($r = $this->adminOnly($request)) return $r;
        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:utilisateurs,email',
            'telephone' => 'nullable|string|max:20',
            'mot_de_passe' => 'required|min:6',
            'role' => 'required|in:parent,encadreur,responsable,admin',
            'langue' => 'nullable|in:fr,wo',
            'actif' => 'boolean',
        ]);
        $data['langue'] = $data['langue'] ?? 'fr';
        $data['actif'] = $data['actif'] ?? true;
        $u = Utilisateur::create($data);
        return response()->json($u->makeHidden(['mot_de_passe']), 201);
    }

    public function update(Request $request, Utilisateur $utilisateur): JsonResponse {
        if ($r = $this->adminOnly($request)) return $r;
        $data = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:utilisateurs,email,' . $utilisateur->id,
            'telephone' => 'nullable|string|max:20',
            'mot_de_passe' => 'nullable|min:6',
            'role' => 'sometimes|in:parent,encadreur,responsable,admin',
            'langue' => 'nullable|in:fr,wo',
            'actif' => 'boolean',
        ]);
        if (empty($data['mot_de_passe'])) unset($data['mot_de_passe']);
        $utilisateur->update($data);
        return response()->json($utilisateur->fresh()->makeHidden(['mot_de_passe']));
    }

    public function destroy(Request $request, Utilisateur $utilisateur): JsonResponse {
        if ($r = $this->adminOnly($request)) return $r;
        if ($utilisateur->id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 422);
        }
        $utilisateur->update(['actif' => false]);
        $utilisateur->tokens()->delete();
        return response()->json(['message' => 'Compte désactivé.']);
    }
}
