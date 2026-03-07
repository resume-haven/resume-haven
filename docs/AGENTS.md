# 🤖 KI-Agenten & Kontext

Diese Datei dient als Brücke zur neuen konsolidierten Kontext-Struktur.

---

## 📚 Zentrale Dokumentation für KI-Agenten

Alle KI-relevanten Informationen sind jetzt in einer klaren Hierarchie organisiert:

### Einstiegspunkt
👉 **[`.github/copilot-instructions.md`](../.github/copilot-instructions.md)**  
Haupteinstieg für GitHub Copilot und andere KI-Agenten.

---

## 🗂️ Kontext-Hierarchie

1. **Agent-Kontext** (Arbeitsregeln)  
   → [`docs/ai/AGENT_CONTEXT.md`](ai/AGENT_CONTEXT.md)  
   CQRS, SOLID, DDD, Quality-Gates, Definition of Done

2. **Projektüberblick**  
   → [`docs/ai/PROJECT_OVERVIEW.md`](ai/PROJECT_OVERVIEW.md)  
   Was ist ResumeHaven? Funktionsumfang, Architektur-Kurzform, Roadmap

3. **Tech Stack**  
   → [`docs/ai/TECH_STACK.md`](ai/TECH_STACK.md)  
   Versionen, Docker-Services, Konfiguration, Make-Kommandos

4. **Architektur** (detailliert)  
   → [`ARCHITECTURE.md`](ARCHITECTURE.md)  
   Domain-Driven Design, CQRS-Strategie, Repository Pattern, Testing

5. **Coding Guidelines**  
   → [`CODING_GUIDELINES.md`](CODING_GUIDELINES.md)  
   PHP-Konventionen, Laravel Best Practices, SOLID-Enforcement

6. **Laravel Boost Regeln**  
   → [`../src/AGENTS.md`](../src/AGENTS.md)  
   Automatisch generierte Guidelines (NICHT manuell editieren!)

---

## 🎯 Für Entwickler

**Wenn du mit KI-Tools arbeitest:**
- Copilot liest automatisch `.github/copilot-instructions.md`
- Alle Regeln sind in `docs/ai/AGENT_CONTEXT.md` definiert
- Architektur-Details in `ARCHITECTURE.md`
- Code-Standards in `CODING_GUIDELINES.md`

**Wenn du die AI-Features des Projekts entwickelst:**
- AI-Analyzer: `app/Services/AiAnalyzer/`
- AI-Agenten: `app/Ai/Agents/`
- AI-Konfiguration: `config/ai.php`
- AI-Provider: Gemini (Production), Mock (Development)

---

## 📖 Weitere Informationen

- **Projekt-Setup:** [`../README.md`](../README.md)
- **Entwicklung:** [`DEVELOPMENT.md`](DEVELOPMENT.md)
- **Debugging:** [`DEBUGGING.md`](DEBUGGING.md)
- **Commit-Plan:** [`../COMMIT_PLAN.md`](../COMMIT_PLAN.md)

