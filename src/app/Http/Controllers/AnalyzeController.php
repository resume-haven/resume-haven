<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\AnalyzeRequestDto;
use App\Services\AnalyzeApplicationService;
use App\Services\AnalysisCacheService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AnalyzeController extends Controller
{
    public function analyze(Request $request): \Illuminate\View\View
    {
        $validated = $request->validate([
            'job_text' => ['required', 'min:30'],
            'cv_text' => ['required', 'min:30'],
        ]);

        $dto = new AnalyzeRequestDto($validated['job_text'], $validated['cv_text']);
        $cacheService = app()->make(AnalysisCacheService::class);
        $result = $cacheService->getByDto($dto);
        $error = null;

        // Demo-Modus: Feste Daten (nur für Layout-Kontrolle)
        $demoMode = false;
        if ($demoMode) {
            $demoJobText = 'SCOPEVISIO: Eine neue Softwaregeneration – cloudbasiert | integriert | automatisiert  Simplify your daily business. Mit einem hoch engagierten Team entwickelt und vertreibt die Scopevisio AG seit 2007 eine Cloud-Unternehmenssoftware (ERP) für den Mittelstand und deckt dabei die Märkte Deutschland und Österreich ab. Alle wichtigen Geschäftsprozesse für die Unternehmenssteuerung sind in einer zentralen Cloud-Lösung integriert: Finanzen, Controlling, Buchhaltung, Beschaffung, Personal, Vertrieb sowie Dokumentenmanagement. Mit dem Einsatz moderner Technologien wie KI und Sprachsteuerung automatisieren und vereinfachen wir Unternehmensprozesse, um unsere Kund:innen für die digitale Zukunft auszurüsten.  Wir haben Großes vor: Wir planen zu wachsen und mittelfristig an die Börse zu gehen!  Zur Verstärkung unseres wachsenden Teams und Geschäfts suchen wir ambitionierte Persönlichkeiten, Teamplayer und Macher!  Dies sind Deine Aufgaben:  Entwicklung, Architektur und Weiterentwicklung von RESTful APIs auf Basis von Laravel 12 Design, Optimierung und Pflege komplexer Datenbankstrukturen in PostgreSQL Implementierung anspruchsvoller Business-Logiken für HR-Prozesse Sicherstellung einer hohen Code-Qualität durch Unit Tests (PHPUnit), automatisierte Tests und Code Reviews Durchführung von Performance-Optimierungen sowie Skalierung unserer Backend-Services Technische Führung und Mentoring von Junior-Entwicklern im Team Integration von Third-Party-APIs, externen Services und Schnittstellen Entwicklung und Pflege von Artisan Commands zur Systemautomatisierung  Das solltest Du mitbringen:   Mindestens 5 Jahre Berufserfahrung in der PHP-Entwicklung Sehr gute Kenntnisse in Laravel (idealerweise 10+) Tiefgreifende Erfahrung mit PHP 8+ Features (Typed Properties, Enums, Attributes) Expertenwissen in PostgreSQL, inkl. komplexer Abfragen und Datenbank-Optimierung Erfahrung in RESTful API-Design, Standards und Best Practices Kenntnisse in Kryptographie und Datensicherheit (z. B. AES, Bcrypt) Gute Kenntnisse in Software-Architektur (DDD, Clean Architecture, SOLID) Praxis mit PHPUnit, idealerweise TDD-Ansatz Sehr gute Deutsch- und gute Englischkenntnisse Nice to have, aber kein Muss: Erfahrung mit Laravel Queues, Horizon und Job-Processing Kenntnisse in Docker und Container-Orchestrierung Erfahrung mit Redis oder vergleichbaren Caching-Technologien Verständnis für DSGVO, Datenschutz und Compliance im HR-Kontext Kenntnisse in Event Sourcing oder CQRS  Das bieten wir Dir:  Eigenverantwortungsvolle Aufgabe in einer agil arbeitenden Mannschaft mit viel Gestaltungsspielraum in einem internationalen Team mit flachen Hierarchien Mobile Work via Cloud und virtual Meetings Einen der schönsten Standorte Bonns - direkt am Rhein! Mitarbeit an einem modernen, zukunftsweisenden und technologisch führenden Cloud–Produktes Attraktive und leistungsgerechte Vergütung Unbefristeter Arbeitsvertrag mit weiteren Entwicklungsperspektiven Individuelles und begleitetes Onboarding mit Coaching– und Reflexionsmöglichkeiten durch Deine Fachabteilung Zahlreiche attraktive Benefits innerhalb der Unternehmensgruppe Corporate Benefits - Portal für Mitarbeiterangebote Tolle Mitarbeiterevents und die besten Kolleg:innen der Welt, verteilt auf viele Standorte  Über uns  Die Scopevisio AG zählt zu den erfolgreichsten Cloud–ERP-Anbietern in Deutschland. Seit 2007 entwickeln wir cloudbasierte Unternehmenssoftware für den Mittelstand. Unsere große Leidenschaft für neue Technologien und der Mut, neue Wege zu gehen, zeichnen uns aus. Wir sind davon überzeugt, dass Unternehmenssoftware im Zeitalter der Digitalisierung neu erfunden werden muss. Gefragt sind mobile, flexible und hochautomatisierte Lösungen. Genau die entwickeln wir – damit Unternehmen für die digitale Zukunft gerüstet sind!  Hersteller und Anbieter von Cloud Unternehmenssoftware Geschäftsmodell als IT–Versorger (SaaS & PaaS & IaaS) Hauptsitz Bonn, Standorte von Tochterfirmen in Wuppertal, Halle (Westf.), Trier, Kaltenkirchen (bei Hamburg) und Wien Bei der Scopevisio AG arbeiten rund 105 Mitarbeitende, in der gesamten Unternehmensgruppe rund 300 Der Umsatz lag 2024 bei > 32 Mio. € Über 7.000 B2B–Kunden';
            $demoCvText = 'php, sql, ...'; // Demo-Lebenslauf
            $dto = new AnalyzeRequestDto($demoJobText, $demoCvText);
            $result = [
                'requirements' => [
                    'Mindestens 5 Jahre Berufserfahrung in der PHP-Entwicklung',
                    'Sehr gute Kenntnisse in Laravel (idealerweise 10+)',
                    'Tiefgreifende Erfahrung mit PHP 8+ Features',
                    'Erfahrung in RESTful API-Design',
                ],
                'experiences' => [
                    'php',
                    'laravel',
                    'symfony',
                    'javascript',
                    'sql',
                    '> 20 Jahre Berufserfahrung',
                ],
                'matches' => [
                    ['requirement' => 'Mindestens 5 Jahre Berufserfahrung in der PHP-Entwicklung', 'experience' => '> 20 Jahre Berufserfahrung'],
                    ['requirement' => 'Sehr gute Kenntnisse in Laravel (idealerweise 10+)', 'experience' => 'laravel'],
                ],
                'gaps' => [
                    'Tiefgreifende Erfahrung mit PHP 8+ Features',
                    'Erfahrung in RESTful API-Design',
                ],
            ];
        }

        if (! $result && ! $demoMode) {
            try {
                $service = app()->make(AnalyzeApplicationService::class);
                $analyzeResult = $service->analyze($dto);
                $result = $analyzeResult->toArray();
                $cacheService->putByDto($dto, $result);
                // Fehlertext aus AnalyzeResultDto übernehmen, falls vorhanden
                if (! empty($result['error'])) {
                    $error = $result['error'];
                }
            } catch (\Throwable $e) {
                $error = 'AI-Analyse fehlgeschlagen';
                $result = [
                    'requirements' => [],
                    'experiences' => [],
                    'matches' => [],
                    'gaps' => [],
                ];
            }
        }

        return view('result', [
            'job_text' => $dto->jobText(),
            'cv_text' => $dto->cvText(),
            'result' => $result,
            'error' => $error,
        ]);
    }
}
