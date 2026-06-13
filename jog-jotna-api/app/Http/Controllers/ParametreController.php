<?php
namespace App\Http\Controllers;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Storage;

class ParametreController extends Controller {
    private const FILE = 'settings.json';

    private function defaults(): array {
        return [
            'village_defaut' => 'Diagambal',
            'lieu_seance_defaut' => 'Centre communautaire Diagambal',
            'score_seuil_alerte_defaut' => 60,
            'score_seuil_urgent_defaut' => 40,
            'nom_programme' => 'JÒG JOTNA',
        ];
    }

    public function show(): JsonResponse {
        $settings = $this->defaults();
        if (Storage::exists(self::FILE)) {
            $settings = array_merge($settings, json_decode(Storage::get(self::FILE), true) ?: []);
        }
        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse {
        $data = $request->validate([
            'village_defaut' => 'sometimes|string|max:100',
            'lieu_seance_defaut' => 'sometimes|string|max:200',
            'score_seuil_alerte_defaut' => 'sometimes|numeric|between:0,100',
            'score_seuil_urgent_defaut' => 'sometimes|numeric|between:0,100',
            'nom_programme' => 'sometimes|string|max:100',
        ]);
        $current = Storage::exists(self::FILE)
            ? array_merge($this->defaults(), json_decode(Storage::get(self::FILE), true) ?: [])
            : $this->defaults();
        Storage::put(self::FILE, json_encode(array_merge($current, $data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return response()->json(['message' => 'Paramètres enregistrés.', 'parametres' => array_merge($current, $data)]);
    }
}
