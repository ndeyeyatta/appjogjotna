<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\{Evaluation,Observation};
use App\Observers\{EvaluationObserver,ObservationObserver};
class AppServiceProvider extends ServiceProvider {
    public function boot(): void {
        Evaluation::observe(EvaluationObserver::class);
        Observation::observe(ObservationObserver::class);
    }
}
