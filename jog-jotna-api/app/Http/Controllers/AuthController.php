<?php
namespace App\Http\Controllers;
use App\Models\Utilisateur;
use Illuminate\Http\{Request,JsonResponse};
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    public function login(Request $request): JsonResponse {
        $request->validate(['email'=>'required|email','mot_de_passe'=>'required|min:6']);
        $u = Utilisateur::where('email',$request->email)->where('actif',true)->first();
        if(!$u||!Hash::check($request->mot_de_passe,$u->mot_de_passe))
            throw ValidationException::withMessages(['email'=>['Identifiants incorrects.']]);
        $u->tokens()->delete();
        $token = $u->createToken('jog-jotna')->plainTextToken;
        return response()->json(['token'=>$token,'utilisateur'=>['id'=>$u->id,'nom_complet'=>$u->nom_complet,'email'=>$u->email,'role'=>$u->role,'langue'=>$u->langue]]);
    }
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Déconnexion réussie.']);
    }
    public function me(Request $request): JsonResponse {
        $u = $request->user();
        return response()->json(['id'=>$u->id,'nom_complet'=>$u->nom_complet,'nom'=>$u->nom,'prenom'=>$u->prenom,'email'=>$u->email,'telephone'=>$u->telephone,'role'=>$u->role,'langue'=>$u->langue,'notifications_non_lues'=>$u->notifications()->where('lu',false)->count()]);
    }
    public function changerMotDePasse(Request $request): JsonResponse {
        $request->validate(['ancien_mot_de_passe'=>'required','nouveau_mot_de_passe'=>'required|min:8|confirmed']);
        $u = $request->user();
        if(!Hash::check($request->ancien_mot_de_passe,$u->mot_de_passe))
            return response()->json(['message'=>'Ancien mot de passe incorrect.'],422);
        $u->update(['mot_de_passe'=>$request->nouveau_mot_de_passe]);
        return response()->json(['message'=>'Mot de passe modifié avec succès.']);
    }
}
