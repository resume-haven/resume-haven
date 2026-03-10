# 🏗️ ResumeHaven – Projektüberblick

## 🎯 Was ist ResumeHaven?

Ein **leichtgewichtiges, KI-gestütztes Analyse-Tool**, das:
- **Stellenausschreibungen**
- **Lebensläufe**

miteinander vergleicht und **strukturiert auswertet**.

**Ziel:** Bewerbern zeigen, wie gut ihr Profil zu einer Stelle passt und wo Lücken bestehen.

---

## 🧱 MVP-Funktionsumfang (aktuell)

### ✅ **Enthalten:**

#### Analyse (KI-gestützt via Gemini)
- Anforderungen aus Stellenausschreibungen extrahieren
- Erfahrungen aus Lebensläufen extrahieren
- Matches zwischen Anforderungen und Erfahrungen finden
- Lücken (Gaps) identifizieren
- Tag-basierte Darstellung (Match-Tags grün, Gap-Tags rot)

#### Scoring & Visualisierung
- Score-Berechnung: `(Matches / (Matches + Gaps)) * 100`
- Fortschrittsbalken mit Farbskala (Rot/Gelb/Grün)
- Bewertungstext ("Geringe/Mittlere/Hohe Übereinstimmung")

#### Performance & Security
- Analyse-Cache (Datenbank, Request-Hash-basiert)
- Input-Validierung (max 50KB, Pattern-Detection)
- Prompt-Injection-Schutz im AI-Analyzer
- Input-Sanitization (Null-Bytes, Whitespace, Line-Endings)

#### Entwicklung
- Mock-AI-Provider (ohne API-Kosten entwickeln)
- Xdebug-Integration (optional)
- 98.2% Test-Coverage
- PHPStan Level 9

---

### ❌ **NICHT im MVP:**

- ❌ Keine User-Accounts
- 🔄 **Lebenslauf-Speicherung (Commit 22 - in Implementierung)**
  - Anonym, token-basiert, verschlüsselt
  - Branch: `feature/commit-22-profile-cv-storage`
  - Detailplan: `docs/PLANNING_COMMIT_22.md`
- ❌ Keine PDF-Generierung
- ❌ Keine öffentliche API
- ❌ Keine E-Mail-Benachrichtigungen (nur Mailpit für Tests)
- ❌ Kein Production-Deployment (aktuell nur Docker-Dev)

---

## 🏗️ Architektur (Kurzform)

### Domain-Driven Design (DDD)

**Bounded Context:** `Analysis`

```
app/Domains/Analysis/
├── Commands/         # CQRS Commands (Write)
├── Handlers/         # Command-Handler (orchestriert UseCases)
├── UseCases/         # Business-Logic
│   ├── ValidateInputUseCase/
│   ├── ExtractDataUseCase/      (geplant, noch nicht genutzt)
│   ├── MatchingUseCase/
│   ├── GapAnalysisUseCase/
│   ├── ScoringUseCase/
│   └── GenerateTagsUseCase/
├── Cache/            # Cache-Layer
│   ├── Actions/
│   └── Repositories/
└── Dto/              # Data Transfer Objects (immutable)
```

### Single-Action-Controller

Controller sind dünn (~34 Zeilen) und nutzen `__invoke()`:

```php
class AnalyzeController extends Controller
{
    public function __invoke(Request $request): View
    {
        // Dispatch Command → Handler → UseCases
        $result = $this->handler->handle($command);
        return view('result', compact('result'));
    }
}
```

### Repository Pattern

Persistence-Abstraktion, kein Raw-SQL außer in Repositories:

```php
// app/Domains/Analysis/Cache/Repositories/AnalysisCacheRepository.php
public function getByHash(string $hash): ?array
{
    return AnalysisCache::where('request_hash', $hash)->first()?->result;
}
```

---

## 🎨 UI/UX-Prinzipien

### Design-System
- **Minimalistisch:** Klar, professionell, keine Ablenkung
- **TailwindCSS v3:** Utility-First
- **Mobile-First:** Responsive (geplant: Commit 20)
- **Dark-Mode:** Geplant (Commit 20a)

### Komponenten
- **Panels:** `rounded-lg, shadow-sm, p-6, bg-white`
- **Buttons:** `bg-blue-600, hover:bg-blue-700, text-white, px-6, py-3, rounded-lg`
- **Match-Tags:** `bg-green-100, text-green-700, px-3, py-1, rounded-full`
- **Gap-Tags:** `bg-red-100, text-red-700, px-3, py-1, rounded-full`
- **Score-Bar:** `bg-green-500` (70-100%), `bg-yellow-500` (40-70%), `bg-red-500` (0-40%)

### Layout-Struktur (result.blade.php)
1. **Score-Panel** (oberste Priorität)
   - Großer Prozentsatz
   - Fortschrittsbalken
   - Bewertungstext
2. **Stellenausschreibung** (read-only)
3. **Lebenslauf** (read-only)
4. **Anforderungen** (extrahierte Requirements)
5. **Erfahrungen** (extrahierte Experiences)
6. **Matches** (grüne Tags)
7. **Gaps** (rote Tags)

---

## 🛡️ Validierungsregeln

### Input-Validierung

#### `job_text`
- **required**
- **string**
- **min:** 30 chars
- **max:** 50KB
- Pattern-Detection: SQL-Keywords, XSS, Event-Handler

#### `cv_text`
- **required**
- **string**
- **min:** 30 chars
- **max:** 50KB
- Pattern-Detection: SQL-Keywords, XSS, Event-Handler

### Security-Layer
1. **Input-Sanitization:**
   - Null-Bytes entfernen
   - Whitespace trimmen
   - Line-Endings normalisieren (`\r\n` → `\n`)

2. **Pattern-Detection:**
   - SQL-Keywords (SELECT, INSERT, UPDATE, DELETE, DROP)
   - XSS (`<script>`, `<iframe>`, `javascript:`)
   - Event-Handler (`onclick=`, `onerror=`)

3. **Prompt-Injection-Schutz:**
   - Strikte System-Regeln im AI-Analyzer
   - Input wird als "UNVERTRAUTER INHALT" behandelt
   - Keine Anweisungen aus Input werden befolgt

---

## 📦 Datenstrukturen (Kern-DTOs)

### AnalyzeRequestDto
```php
readonly class AnalyzeRequestDto
{
    public function __construct(
        public string $jobText,
        public string $cvText,
    ) {}
    
    public function requestHash(): string {
        return hash('sha256', $this->jobText . $this->cvText);
    }
}
```

### AnalyzeResultDto
```php
readonly class AnalyzeResultDto
{
    public function __construct(
        public string $job_text,
        public string $cv_text,
        public array $requirements,      // array<int, string>
        public array $experiences,       // array<int, string>
        public array $matches,           // array<int, array{requirement: string, experience: string}>
        public array $gaps,              // array<int, string>
        public ?string $error,
        public ?array $tags,             // array{matches: array<...>, gaps: array<...>}
    ) {}
}
```

### ScoreResultDto
```php
readonly class ScoreResultDto
{
    public function __construct(
        public int $percentage,          // 0-100
        public string $rating,           // "Geringe/Mittlere/Hohe Übereinstimmung"
        public string $bgColor,          // Tailwind-Klasse
        public string $textColor,        // Tailwind-Klasse
        public string $barColor,         // Tailwind-Klasse
        public int $matchCount,
        public int $gapCount,
    ) {}
}
```

---

## 🔄 Request-Flow (vereinfacht)

```
1. User submits Form (job_text + cv_text)
   ↓
2. AnalyzeController::__invoke()
   ↓
3. ValidateInputAction (sanitize, pattern-detect)
   ↓
4. AnalyzeJobAndResumeCommand (DTO)
   ↓
5. AnalyzeJobAndResumeHandler
   ├─→ Cache-Check (GetCachedAnalysisAction)
   ├─→ AI-Analyse (GeminiAiAnalyzer / MockAiAnalyzer)
   ├─→ Matching (MatchingUseCase)
   ├─→ Gap-Analysis (GapAnalysisUseCase)
   ├─→ Tag-Generation (GenerateTagsAction)
   └─→ Cache-Store (StoreCachedAnalysisAction)
   ↓
6. ScoringUseCase::handle()
   ↓
7. BuildAnalyzeViewDataAction (DTO für View)
   ↓
8. result.blade.php (UI)
```

---

## 🚫 Was das MVP NICHT tut

### Funktional
- ❌ Keine Lebenslauf-Speicherung (kommt in Phase 2)
- ❌ Keine KI-Empfehlungen ("Wie verbessere ich meinen CV?")
- ❌ Keine Verlaufs-Historie (kein "Meine Analysen")
- ❌ Keine Vergleichs-Funktion (mehrere Jobs gleichzeitig)
- ❌ Keine PDF/Word-Upload (nur Plain-Text)
- ❌ Kein Export (PDF/Word-Download)

### Technisch
- ❌ Keine User-Accounts / Authentication
- ❌ Keine öffentliche API
- ❌ Kein Production-Hosting (nur Docker-Dev)
- ❌ Keine E-Mail-Integration (nur Mailpit für Tests)
- ❌ Keine Real-Time-Collaboration
- ❌ Keine Internationalisierung (nur Deutsch)

---

## 📅 Roadmap (Highlights)

### Phase 2 (Post-MVP)
- **Commit 19:** KI-Empfehlungen & Verbesserungsvorschläge
- **Commit 20:** Responsive Layout & Mobile-First
- **Commit 20a:** Dark-Mode Support
- **Commit 22:** Lebenslauf-Speicherung (anonym)

### Phase 3 (Erweiterung)
- **Bounded Context `Profile`:**
  - User-Accounts
  - Lebenslauf-Verwaltung
  - Präferenzen
- **Bounded Context `Recommendations`:**
  - KI-Empfehlungen
  - Verbesserungsvorschläge
  - Beispiel-Formulierungen

### Phase 4 (Analytics)
- **Bounded Context `Reporting`:**
  - Analyse-Historie
  - Statistiken
  - Export-Funktionen

---

## 📚 Siehe auch

- **Architektur:** `docs/ARCHITECTURE.md`
- **Coding Guidelines:** `docs/CODING_GUIDELINES.md`
- **Tech Stack:** `docs/ai/TECH_STACK.md`
- **Agent-Kontext:** `docs/ai/AGENT_CONTEXT.md`
- **Commit-Plan:** `COMMIT_PLAN.md`

---

**Letzte Aktualisierung**: 2026-03-09  
**Version**: 2.1 (konsolidierter KI-Dokumentationskontext)
