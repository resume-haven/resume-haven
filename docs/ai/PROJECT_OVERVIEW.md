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

#### Profile / CV-Speicherung
- Anonyme CV-Speicherung über Token-Link
- URL-safe Base64-Token mit hoher Entropie
- Verschlüsselte Speicherung des CVs in `stored_resumes`
- Wiederherstellung des gespeicherten CVs über `/profile/load/{token}`

#### Performance & Security
- Analyse-Cache (Datenbank, Request-Hash-basiert)
- Input-Validierung (max 50KB, Pattern-Detection)
- Prompt-Injection-Schutz im AI-Analyzer
- Input-Sanitization (Null-Bytes, Whitespace, Line-Endings)
- Fehlerbehandlung für ungültige Resume-Tokens und defekte Payloads

#### Entwicklung
- Mock-AI-Provider (ohne API-Kosten entwickeln)
- Xdebug-Integration (optional)
- 98.2% Test-Coverage
- PHPStan Level 9

---

### ❌ **NICHT im MVP:**

- ❌ Keine User-Accounts
- ❌ Keine Multi-CV-Verwaltung
- ❌ Keine PDF-Generierung
- ❌ Keine öffentliche API
- ❌ Keine E-Mail-Benachrichtigungen (nur Mailpit für Tests)
- ❌ Kein Production-Deployment (aktuell nur Docker-Dev)
- ⚠️ Keine finale User-basierte Verschlüsselung (MVP nutzt Token als Secret, spaeteres Refactoring eingeplant)

---

## 🏗️ Architektur (Kurzform)

### Domain-Driven Design (DDD)

**Bounded Contexts:** `Analysis`, `Profile`

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

app/Domains/Profile/
├── Commands/         # CV speichern
├── Queries/          # CV per Token laden
├── Handlers/         # Orchestrierung Store/Load
├── Actions/          # Token, Encrypt, Decrypt
├── Repositories/     # StoredResume-Persistenz
└── Dto/              # StoreResumeDto, ResumeTokenDto, LoadedResumeDto
```

### Single-Action-Controller

Controller sind dünn und nutzen `__invoke()`:

```php
class StoreResumeController extends Controller
{
    public function __invoke(StoreResumeRequest $request, Dispatcher $dispatcher): RedirectResponse
    {
        /** @var ResumeTokenDto $tokenDto */
        $tokenDto = $dispatcher->dispatch(
            new StoreResumeCommand(new StoreResumeDto($request->validated('cv_text')))
        );

        return redirect()->route('analyze')
            ->with('resume_token', $tokenDto->token);
    }
}
```

### Repository Pattern

Persistence-Abstraktion, kein Raw-SQL außer in Repositories:

```php
// app/Domains/Profile/Repositories/ProfileRepository.php
public function getByToken(string $token): ?StoredResume
{
    return StoredResume::query()->where('token', $token)->first();
}
```

---

## 🎨 UI/UX-Prinzipien

### Design-System
- **Minimalistisch:** Klar, professionell, keine Ablenkung
- **TailwindCSS v3:** Utility-First
- **Mobile-First:** Responsive umgesetzt
- **Dark-Mode:** Implementiert mit Toggle und Persistierung

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
- ❌ Keine User-Accounts / Authentication
- ❌ Keine Multi-CV-Verwaltung
- ❌ Keine Verlaufs-Historie (kein "Meine Analysen")
- ❌ Keine Vergleichs-Funktion (mehrere Jobs gleichzeitig)
- ❌ Keine PDF/Word-Upload (nur Plain-Text)
- ❌ Kein Export (PDF/Word-Download)

### Technisch
- ❌ Keine öffentliche API
- ❌ Kein Production-Hosting (nur Docker-Dev)
- ❌ Keine E-Mail-Integration (nur Mailpit für Tests)
- ❌ Keine Real-Time-Collaboration
- ❌ Keine Internationalisierung (nur Deutsch)
- ⚠️ Noch keine User-basierte Verschlüsselung fuer gespeicherte CVs

---

## 📅 Roadmap (Highlights)

### Naechste Schritte
- **Commit 19:** Nachziehen/abschliessen der historisch uebersprungenen Planungsinhalte
- **Commit 23+:** CI/CD, Deployment, weitere Produktfeatures
- **Profile-Weiterentwicklung:** Benutzerkonten, mehrere CVs, ueberarbeitete Verschluesselungsstrategie
- **Recommendations/Reporting:** Weitere Entkopplung in eigene Kontexte nach MVP

### Mittelfristig
- **Bounded Context `Profile`:**
  - User-Accounts
  - Lebenslauf-Verwaltung
  - Praeferenzen
  - sichere, userbasierte Verschluesselung
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

**Letzte Aktualisierung**: 2026-03-10  
**Version**: 2.2 (Commit-22-Status aktualisiert)
