<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RapportExport implements FromArray, WithHeadings, WithTitle {
    public function __construct(private array $donnees, private string $titre) {}

    public function array(): array {
        $rows = [];
        foreach ($this->donnees['evaluations'] ?? [] as $e) {
            $rows[] = [
                $e->enfant->nom_complet ?? '',
                $e->dimension,
                $e->score_global,
                $e->retard_detecte ? 'Oui' : 'Non',
                $e->date_eval?->format('d/m/Y') ?? '',
            ];
        }
        if (empty($rows)) {
            $rows[] = ['—', '—', '—', '—', '—'];
        }
        return $rows;
    }

    public function headings(): array {
        return ['Enfant', 'Dimension', 'Score (%)', 'Retard', 'Date'];
    }

    public function title(): string {
        return substr($this->titre, 0, 31);
    }
}
