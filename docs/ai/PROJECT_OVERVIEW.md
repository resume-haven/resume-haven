# 🏗️ ResumeHaven – Project Overview

## 🎯 What is ResumeHaven?

A **lightweight, AI-powered analytics tool** that:
- **Job advertisements**
- **CVs**

compares with each other and **evaluates them in a structured manner**.

**Goal:** Show applicants how well their profile fits a position and where there are gaps.

---

## 🧱 MVP feature set (current)

### ✅ **Included:**

#### Analysis (AI-supported via Gemini)
- Extract requirements from job postings
- Extract experiences from resumes
- Find matches between requirements and experiences
- Identify gaps
- Tag-based display (match tags green, gap tags red)

#### Scoring & Visualization
- Score calculation: `(Matches / (Matches + Gaps)) * 100`
- Progress bar with color scale (red/yellow/green)
- Rating text (“Low/Medium/High Agreement”)

#### Profiles / CV storage
- Anonymous CV storage via token link
- URL-safe, high-entropy Base64 token
- Encrypted storage of the CV in `stored_resumes`
- Restoration of saved CV via `/profile/load/{token}`
- **Auth (Commit 29):** Registration / Login / Logout via Laravel Breeze
- **Auth (Commit 29):** Roll `user` / `admin` to `User` model
- **Auth (Commit 29):** Auto-Claim — anonymous session CVs are assigned to the user upon login

#### Performance & Security
- Analysis cache (database, request hash based)
- Input validation (max 50KB, pattern detection)
- Prompt injection protection in AI Analyzer
- Input sanitization (zero bytes, whitespace, line endings)
- Error handling for invalid resume tokens and broken payloads

#### Development
- Mock AI provider (develop without API costs)
- Xdebug integration (optional)
- 98.2% test coverage
- PHPStan Level 9

---

### ❌ **NOT in MVP:**

- ❌ No multi-CV management (planned commit 30+)
- ❌ No PDF generation
- ❌ No public API
- ❌ No email notifications (only Mailpit for testing, verification disabled)
- ❌ No production deployment (currently only Docker Dev)
- ⚠️ No final user-based encryption (MVP uses tokens as secrets, refactoring planned)
- ⚠️ `resume_token` in Session (singular) — Tech-Debt, will be converted to array with CV management

---

## 🏗️ Architecture (short form)

### Domain Driven Design (DDD)

**Bounded Contexts:** `Analysis`, `Profile`

```
app/Domains/Analysis/
├── Commands/         # CQRS Commands (Write)
├── Handlers/         # Command handlers (orchestrates UseCases)
├── UseCases/         # Business logic
│   ├── ValidateInputUseCase/
│   ├── ExtractDataUseCase/      (planned, not yet active)
│   ├── MatchingUseCase/
│   ├── GapAnalysisUseCase/
│   ├── ScoringUseCase/
│   └── GenerateTagsUseCase/
├── Cache/            # Cache layer
│   ├── Actions/
│   └── Repositories/
└── Dto/              # Data Transfer Objects (immutable)

app/Domains/Profile/
├── Commands/         # Store CV
├── Queries/          # Load CV by token
├── Handlers/         # Orchestration Store/Load
├── Actions/          # Token, Encrypt, Decrypt
├── Repositories/     # StoredResume persistence
└── Dto/              # StoreResumeDto, ResumeTokenDto, LoadedResumeDto
```

### Single action controller

Controllers are thin and use `__invoke()`:

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

Persistence abstraction, no raw SQL except in repositories:

```php
// app/Domains/Profile/Repositories/ProfileRepository.php
public function getByToken(string $token): ?StoredResume
{
    return StoredResume::query()->where('token', $token)->first();
}
```

---

## 🎨 UI/UX principles

### Design system
- **Minimalist:** Clear, professional, no distractions
- **TailwindCSS v3:** Utility-First
- **Mobile-First:** Implemented responsively
- **Dark Mode:** Implemented with toggle and persistence

### Components
- **Panels:** `rounded-lg, shadow-sm, p-6, bg-white`
- **Buttons:** `bg-blue-600, hover:bg-blue-700, text-white, px-6, py-3, rounded-lg`
- **Match Tags:** `bg-green-100, text-green-700, px-3, py-1, rounded-full`
- **Gap Tags:** `bg-red-100, text-red-700, px-3, py-1, rounded-full`
- **Score Bar:** `bg-green-500` (70-100%), `bg-yellow-500` (40-70%), `bg-red-500` (0-40%)

### Layout structure (result.blade.php)
1. **Score Panel** (top priority)
   - Big percentage
   - Progress bar
   - Review text
2. **Job advertisement** (read-only)
3. **CV** (read only)
4. **Requirements** (extracted requirements)
5. **Experiences** (extracted experiences)
6. **Matches** (green tags)
7. **Gaps** (red tags)

---

## 🛡️ Validation rules

### Input validation

#### `job_text`
- **required**
- **string**
- **min:** 30 chars
- **max:** 50KB
- Pattern detection: SQL keywords, XSS, event handlers

#### `cv_text`
- **required**
- **string**
- **min:** 30 chars
- **max:** 50KB
- Pattern detection: SQL keywords, XSS, event handlers

### Security layer
1. **Input Sanitization:**
   - Remove zero bytes
   - Trim whitespace
   - Normalize line endings (`\r\n` → `\n`)

2. **Pattern detection:**
   - SQL keywords (SELECT, INSERT, UPDATE, DELETE, DROP)
   - XSS (`<script>`, `<iframe>`, `javascript:`)
   - Event handlers (`onclick=`, `onerror=`)

3. **Prompt Injection Protection:**
   - Strict system rules in the AI ​​Analyzer
   - Input is treated as “UNTRUSTED CONTENT”.
   - No instructions from input are followed

---

## 📦 Data Structures (Core DTOs)

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
        public string $rating,           // "Low/Medium/High Match"
        public string $bgColor,          // Tailwind class
        public string $textColor,        // Tailwind class
        public string $barColor,         // Tailwind class
        public int $matchCount,
        public int $gapCount,
    ) {}
}
```

---

## 🔄 Request flow (simplified)

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
   ├─→ Cache check (GetCachedAnalysisAction)
   ├─→ AI analysis (GeminiAiAnalyzer / MockAiAnalyzer)
   ├─→ Matching (MatchingUseCase)
   ├─→ Gap analysis (GapAnalysisUseCase)
   ├─→ Tag generation (GenerateTagsAction)
   └─→ Cache store (StoreCachedAnalysisAction)
   ↓
6. ScoringUseCase::handle()
   ↓
7. BuildAnalyzeViewDataAction (DTO for view)
   ↓
8. result.blade.php (UI)
```

---

## 🚫 What the MVP DOES NOT do

### Functional
- ❌ No multi-CV management
- ❌ No history (no “My Analyzes”)
- ❌ No comparison function (several jobs at the same time)
- ❌ No PDF/Word upload (plain text only)
- ❌ No export (PDF/Word download)

### Technically
- ❌ No public API
- ❌ No production hosting (Docker-Dev only)
- ❌ No email integration (only Mailpit for testing)
- ❌ No real-time collaboration
- ❌ No internationalization (English UI planned)
  ⚠️ No user-based encryption for saved CVs yet

---

## 📅 Roadmap (Highlights)

### Next Steps
- **Commit 29 (current):** Auth + Roles + Claim Flow (`feature/commit-29-auth-roles-claim`)
- **Commit 30+:** CV management (user dashboard, multi-CV, `resume_tokens[]` array)
- **Recommendations/Reporting:** Further decoupling into your own contexts after MVP

### Medium term
- **Bounded Context `Profile`:**
  - User accounts
  - Resume management
  - Preferences
  - Secure, user-based encryption
- **Bounded Context `Reporting`:**
  - Analysis history
  - statistics
  - Export functions

---

## 📚 See then

- **Architecture:** `docs/ARCHITECTURE.md`
- **Coding Guidelines:** `docs/CODING_GUIDELINES.md`
- **Tech Stack:** `docs/ai/TECH_STACK.md`
- **Agent Context:** `docs/ai/AGENT_CONTEXT.md`
- **Commit plan:** `COMMIT_PLAN.md`

---

**Last updated**: 2026-04-21
**Version**: 2.3 (Commit-29 Auth/Roles/Claim-Flow entered, NOT-MVP updated)
