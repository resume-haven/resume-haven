# GitHub Pages Setup für ResumeHaven

Diese Anleitung beschreibt, wie die Dokumentation auf GitHub Pages veröffentlicht wird.

---

## 📋 Voraussetzungen

- Repository auf GitHub
- Admin-Zugriff auf das Repository
- `docs/` Verzeichnis mit Dokumentation (✅ bereits vorhanden)

---

## 🚀 GitHub Pages aktivieren

### Schritt 1: Repository Settings öffnen

1. Gehe zu deinem Repository auf GitHub
2. Klicke auf **Settings** (⚙️)
3. Scrolle im linken Menü zu **Pages**

### Schritt 2: Source konfigurieren

1. **Source**: Wähle `Deploy from a branch`
2. **Branch**: Wähle `main` (oder `master`)
3. **Folder**: Wähle `/docs`
4. Klicke auf **Save**

### Schritt 3: Theme auswählen (optional)

1. Im Pages-Bereich auf **Choose a theme** klicken
2. Ein Theme auswählen (z.B. Cayman, Minimal, Slate)
3. Oder: `_config.yml` manuell anpassen (bereits vorhanden)

### Schritt 4: Warten

GitHub Pages baut die Seite automatisch. Das dauert ca. 1-2 Minuten.

---

## 🌐 URL

Deine Dokumentation ist dann verfügbar unter:

```
https://<username>.github.io/<repository-name>/
```

Beispiel:
```
https://guidoschade.github.io/resume-haven/
```

---

## 📁 Aktuelle Struktur (GitHub Pages-kompatibel)

```
resume-haven/
├── _config.yml              # Jekyll-Konfiguration (Root)
├── README.md                # Projekt-Hauptseite
│
└── docs/                    # GitHub Pages Source
    ├── _config.yml          # Jekyll-Konfiguration (docs/)
    ├── index.md             # Haupt-Index (wird zu /)
    ├── README.md            # Fallback (Kopie von index.md)
    ├── ARCHITECTURE.md      # Wird zu /ARCHITECTURE
    ├── CODING_GUIDELINES.md # Wird zu /CODING_GUIDELINES
    ├── AGENTS.md
    ├── ROADMAP.md
    ├── CONTRIBUTING.md
    └── REFACTORING_SUMMARY.md
```

### URLs nach Veröffentlichung:

| Datei | URL |
|-------|-----|
| `docs/index.md` | `https://username.github.io/repo/` |
| `docs/ARCHITECTURE.md` | `https://username.github.io/repo/ARCHITECTURE` |
| `docs/CODING_GUIDELINES.md` | `https://username.github.io/repo/CODING_GUIDELINES` |

---

## ✅ Checkliste

- [x] `docs/` Verzeichnis mit Markdown-Dateien erstellt
- [x] `docs/index.md` als Haupt-Index erstellt
- [x] `docs/README.md` als Fallback erstellt
- [x] `docs/_config.yml` für Jekyll erstellt
- [x] Links in Markdown-Dateien sind relativ (z.B. `[Link](ARCHITECTURE.md)`)
- [ ] GitHub Pages in Repository Settings aktiviert
- [ ] Theme ausgewählt (oder in `_config.yml` gesetzt)
- [ ] Nach 1-2 Minuten: URL testen

---

## 🎨 Themes

GitHub Pages unterstützt folgende Jekyll-Themes standardmäßig:

1. **Cayman** (modern, clean) - **empfohlen** ✅
2. **Minimal** (sehr einfach)
3. **Slate** (dunkel)
4. **Architect** (technisch)
5. **Tactile** (klassisch)
6. **Dinky** (kompakt)
7. **Leap Day** (frisch)
8. **Merlot** (elegant)
9. **Midnight** (dunkel, techno)
10. **Modernist** (minimalistisch)
11. **Time Machine** (retro)
12. **Hacker** (Terminal-Style)

Aktuell konfiguriert: **Cayman**

Theme ändern in `docs/_config.yml`:
```yaml
theme: jekyll-theme-minimal  # Beispiel
```

---

## 🔧 Anpassungen

### Navigation anpassen

Editiere `docs/_config.yml`:
```yaml
navigation:
  - title: Home
    url: /
  - title: Architektur
    url: /ARCHITECTURE
```

### Custom Domain

1. In Repository Settings → Pages → Custom domain
2. Domain eintragen (z.B. `docs.resumehaven.io`)
3. DNS-Records bei Domain-Provider setzen

---

## 🐛 Troubleshooting

### Seite zeigt nicht

1. **Prüfen**: Settings → Pages → "Your site is ready to be published at..."
2. **Warten**: Build dauert 1-2 Minuten
3. **Actions prüfen**: GitHub Actions → "pages-build-deployment"

### Links funktionieren nicht

1. **Relative Links nutzen**: `[Text](ARCHITECTURE.md)` statt absolute URLs
2. **Keine `.html` Extension**: GitHub Pages konvertiert `.md` automatisch

### Theme wird nicht angewendet

1. **Cache leeren**: Strg+F5 im Browser
2. **_config.yml prüfen**: Syntax korrekt?
3. **Build-Log prüfen**: GitHub Actions → pages-build-deployment

---

## 📚 Weitere Infos

- **GitHub Pages Docs**: https://docs.github.com/en/pages
- **Jekyll Docs**: https://jekyllrb.com/docs/
- **Supported Themes**: https://pages.github.com/themes/

---

**Letzte Aktualisierung**: 2026-03-02

