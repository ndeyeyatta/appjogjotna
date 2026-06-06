<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8">
<style>
* { margin:0;padding:0;box-sizing:border-box; }
body { font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1a1a1a; }
.header { background:#1F4E79;color:white;padding:16px 24px; }
.header h1 { font-size:15px;font-weight:bold; }
.header p  { font-size:9px;color:#b3cde3;margin-top:2px; }
.section { margin:14px 24px 0; }
.section-title { background:#D6E4F0;color:#1F4E79;padding:5px 10px;font-weight:bold;font-size:11px;margin-bottom:8px; }
table { width:100%;border-collapse:collapse;font-size:10px; }
th { background:#2E75B6;color:white;padding:5px 8px;text-align:left; }
td { padding:4px 8px;border-bottom:1px solid #e5e7eb; }
tr:nth-child(even) td { background:#f8fafc; }
.kpi-grid { display:table;width:100%;margin-bottom:10px; }
.kpi { display:table-cell;width:25%;background:#f1f5f9;border:1px solid #e2e8f0;padding:8px;text-align:center; }
.kpi .val { font-size:20px;font-weight:bold;color:#1F4E79; }
.kpi .lbl { font-size:9px;color:#6b7280; }
.badge-urgent  { background:#fee2e2;color:#991b1b;padding:1px 5px;border-radius:3px; }
.badge-modere  { background:#fef3c7;color:#92400e;padding:1px 5px;border-radius:3px; }
.badge-normal  { background:#d1fae5;color:#065f46;padding:1px 5px;border-radius:3px; }
.footer { margin-top:20px;padding:8px 24px;border-top:1px solid #e5e7eb;text-align:center;color:#9ca3af;font-size:9px; }
</style></head>
<body>
<div class="header">
  <h1>RAPPORT SEMESTRIEL — PROGRAMME DE SUIVI DE L'ENFANT — JÒG JOTNA, Diagambal</h1>
  <p>Période : {{ $periode_debut }} → {{ $periode_fin }} &nbsp;|&nbsp; Généré le : {{ now()->format('d/m/Y à H:i') }}</p>
</div>
<div class="section">
  <div class="section-title">1. RÉSUMÉ EXÉCUTIF</div>
  <div class="kpi-grid">
    <div class="kpi"><div class="val">{{ $total_enfants }}</div><div class="lbl">Enfants suivis</div></div>
    <div class="kpi"><div class="val" style="color:#C00000;">{{ $alertes->where('niveau','urgent')->count() }}</div><div class="lbl">Alertes urgentes</div></div>
    <div class="kpi"><div class="val">{{ $evaluations->count() }}</div><div class="lbl">Évaluations</div></div>
    <div class="kpi"><div class="val">{{ $nouvelles_inscriptions??0 }}</div><div class="lbl">Nouvelles inscriptions</div></div>
  </div>
</div>
<div class="section">
  <div class="section-title">2. ENFANTS EN ALERTE ({{ $alertes->count() }})</div>
  <table>
    <tr><th>Enfant</th><th>Âge</th><th>Village</th><th>Type</th><th>Niveau</th></tr>
    @foreach($alertes->take(20) as $a)
    <tr>
      <td><strong>{{ $a->enfant?->nom_complet??'—' }}</strong></td>
      <td>{{ $a->enfant?->age_ans??'—' }}</td>
      <td>{{ $a->enfant?->village??'—' }}</td>
      <td>{{ ucfirst(str_replace('_',' ',$a->type_alerte)) }}</td>
      <td><span class="badge-{{ $a->niveau }}">{{ ucfirst($a->niveau) }}</span></td>
    </tr>
    @endforeach
  </table>
</div>
<div class="section" style="margin-top:12px;">
  <div class="section-title">3. ÉTAT NUTRITIONNEL</div>
  <table>
    <tr><th>Statut</th><th>Nombre</th><th>Pourcentage</th></tr>
    @php $total=$stats_nutritionnel->sum('total')?:1; @endphp
    @foreach($stats_nutritionnel as $s)
    <tr>
      <td><span class="badge-{{ $s->statut_nutritionnel==='normal'?'normal':($s->statut_nutritionnel==='malnutrition_severe'?'urgent':'modere') }}">{{ ucfirst(str_replace('_',' ',$s->statut_nutritionnel)) }}</span></td>
      <td>{{ $s->total }}</td>
      <td>{{ round(($s->total/$total)*100,1) }}%</td>
    </tr>
    @endforeach
  </table>
</div>
<div class="footer">
  Rapport généré automatiquement par la Plateforme JÒG JOTNA v1.0 — Document confidentiel — {{ now()->year }}
</div>
</body></html>
