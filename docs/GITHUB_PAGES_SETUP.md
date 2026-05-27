# GitHub Pages Setup for ResumeHaven

This guide describes how to publish documentation to GitHub Pages.

---

## 📋 Requirements

- Repository on GitHub
- Admin access to the repository
- `docs/` Directory with documentation (✅ already exists)

---

## 🚀 Enable GitHub Pages

### Step 1: Open Repository Settings

1. Go to your repository on GitHub
2. Click on **Settings** (⚙️)
3. Scroll to **Pages** in the left menu

### Step 2: Configure Source

1. **Source**: Select `Deploy from a branch`
2. **Branch**: Select `main` (or `master`)
3. **Folder**: Select `/docs`
4. Click on **Save**

### Step 3: Select theme (optional)

1. In the Pages area click on **Choose a theme**
2. Select a theme (e.g. Cayman, Minimal, Slate)
3. Or: adjust `_config.yml` manually (already exists)

### Step 4: Wait

GitHub Pages builds the page automatically. This takes about 1-2 minutes.

---

## 🌐 URL

Your documentation will then be available at:

```
https://<username>.github.io/<repository-name>/
```

Example:
```
https://guidoschade.github.io/resume-haven/
```

---

## 📁 Current structure (GitHub Pages compatible)

```
resume-haven/
├── _config.yml              # Jekyll configuration (Root)
├── README.md                # Project main page
│
└── docs/                    # GitHub Pages Source
    ├── _config.yml          # Jekyll configuration (docs/)
    ├── index.md             # Main index (becomes /)
    ├── README.md            # Fallback (copy of index.md)
    ├── ARCHITECTURE.md      # Becomes /ARCHITECTURE
    ├── CODING_GUIDELINES.md # Becomes /CODING_GUIDELINES
    ├── AGENTS.md
    ├── ROADMAP.md
    ├── CONTRIBUTING.md
    └── REFACTORING_SUMMARY.md
```

### URLs after publication:

| File | URL |
|-------|-----|
| `docs/index.md` | `https://username.github.io/repo/` |
| `docs/ARCHITECTURE.md` | `https://username.github.io/repo/ARCHITECTURE` |
| `docs/CODING_GUIDELINES.md` | `https://username.github.io/repo/CODING_GUIDELINES` |

---

## ✅ Checklist

- [x] `docs/` Directory created with Markdown files
- [x] `docs/index.md` created as main index
- [x] `docs/README.md` created as a fallback
- [x] `docs/_config.yml` created for Jekyll
- [x] Links in Markdown files are relative (e.g. `[Link](ARCHITECTURE.md)`)
- [ ] GitHub Pages enabled in Repository Settings
- [ ] Theme selected (or set in `_config.yml`)
- [ ] After 1-2 minutes: Test URL

---

## 🎨 Themes

GitHub Pages supports the following Jekyll themes by default:

1. **Cayman** (modern, clean) - **recommended** ✅
2. **Minimal** (very simple)
3. **Slate** (dark)
4. **Architect** (technical)
5. **Tactile** (classic)
6. **Dinky** (compact)
7. **Leap Day** (fresh)
8. **Merlot** (elegant)
9. **Midnight** (dark, techno)
10. **Modernist** (minimalist)
11. **Time Machine** (retro)
12. **Hacker** (Terminal style)

Currently configured: **Cayman**

Change theme to `docs/_config.yml`:
```yaml
theme: jekyll-theme-minimal  # Example
```

---

## 🔧 Customizations

### Customize navigation

Edit `docs/_config.yml`:
```yaml
navigation:
  - title: Home
    url: /
  - title: Architecture
    url: /ARCHITECTURE
```

### Custom domain

1. In Repository Settings → Pages → Custom domain
2. Enter domain (e.g. `docs.resumehaven.io`)
3. Set DNS records with domain providers

---

## 🐛 Troubleshooting

### Page not showing

1. **Check**: Settings → Pages → "Your site is ready to be published at..."
2. **Wait**: Build takes 1-2 minutes
3. **Check Actions**: GitHub Actions → "pages-build-deployment"

### Links don't work

1. **Use relative links**: `[Text](ARCHITECTURE.md)` instead of absolute URLs
2. **No `.html` Extension**: GitHub Pages automatically converts `.md`

### Theme is not applied

1. **Clear cache**: Ctrl+F5 in the browser
2. **Check _config.yml**: Syntax correct?
3. **Check build log**: GitHub Actions → pages-build-deployment

---

## 📚 More info

- **GitHub Pages Docs**: https://docs.github.com/en/pages
- **Jekyll Docs**: https://jekyllrb.com/docs/
- **Supported Themes**: https://pages.github.com/themes/

---

**Last updated**: 2026-03-02
