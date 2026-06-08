# DESIGN.md — SPK Influencer SAW

Design system for high-fidelity responsive prototype: **Sistem Pendukung Keputusan Penentuan Influencer** for PT. Behaestex.

Audience: dosen penguji. Output must feel credible for skripsi/demo review: clean, structured, data-forward, not decorative.

## Direction

Modern minimal — Linear / Vercel posture.

- Quiet, precise, software-native.
- Near-greyscale palette with one cobalt accent.
- Hairline borders, no heavy shadows.
- Content-led layouts; show product state, formulas, tables, validation.
- Accent use restrained: primary CTA, active state, one key status or chart highlight.

## Tokens

Use these tokens verbatim in `:root` for new HTML artifacts.

```css
:root {
  --bg:      oklch(99% 0.002 240);
  --surface: oklch(100% 0 0);
  --fg:      oklch(18% 0.012 250);
  --muted:   oklch(54% 0.012 250);
  --border:  oklch(92% 0.005 250);
  --accent:  oklch(58% 0.18 255);

  --font-display: -apple-system, BlinkMacSystemFont, 'SF Pro Display', system-ui, sans-serif;
  --font-body:    -apple-system, BlinkMacSystemFont, 'SF Pro Text', system-ui, sans-serif;
  --font-mono:    'JetBrains Mono', 'IBM Plex Mono', ui-monospace, Menlo, monospace;

  --accent-soft: color-mix(in oklch, var(--accent) 11%, transparent);
  --fg-soft: color-mix(in oklch, var(--fg) 5%, transparent);
  --ok: oklch(62% 0.14 145);
  --warn: oklch(68% 0.14 70);
  --danger: oklch(58% 0.16 25);
  --radius: 12px;
  --radius-lg: 18px;
  --gutter: clamp(16px, 3vw, 32px);
  --container: 1440px;
}
```

## Typography

- Body: `15px / 1.5`, `var(--font-body)`.
- H1: `clamp(38px, 5vw, 68px)`, line-height `.98`, letter-spacing `-0.025em`.
- H2: `clamp(24px, 3vw, 34px)`, line-height `1.08`.
- Eyebrow: `var(--font-mono)`, `12px`, uppercase, letter-spacing `.08em`, accent color.
- Numerics: `var(--font-mono)` with `font-variant-numeric: tabular-nums`.
- Use `text-wrap: pretty` for prose and `text-wrap: balance` for headings.

## Layout

- Desktop app shell: `272px` sticky sidebar + flexible main column.
- Sticky topbar with translucent `var(--bg)` and blur.
- Main content max-width: `1440px`; padding: `28px var(--gutter) 72px`.
- Section rhythm: cards grouped in 2-column grids, then collapse to 1-column on mobile.
- Mobile: hide sidebar, show horizontal pill navigation under topbar.
- Tables must overflow horizontally instead of shrinking columns below readability.

## Components

### Card

- Background `var(--surface)`.
- Border `1px solid var(--border)`.
- Radius `var(--radius-lg)`.
- Padding `20px` by default.
- No drop shadow.

### Buttons

- Primary: accent background, accent border, surface text.
- Secondary: surface background, border, foreground text.
- Radius `10px`, padding `10px 14px`, font-weight `520`.
- Hover may lift `translateY(-1px)` only; no glow.

### Inputs

- Border `1px solid var(--border)`.
- Surface background.
- Radius `10px`.
- Padding `11px 12px`.
- Labels muted, `13px`.

### Pills / Badges

- Radius `999px`.
- Border `1px solid var(--border)`.
- Padding `4px 9px`.
- Use status colors only for semantic state: valid, warning, danger, active.

### Tables

- Full-width, `border-collapse: collapse`.
- Header cells: mono, uppercase, muted, `11px`.
- Cell padding `12px 14px`.
- Row borders only; no zebra striping.
- Numeric columns right-aligned with tabular mono.

## Screens

Prototype scope includes six screens/sections:

1. Dashboard: summary of influencer count, criteria count, target runtime, data validity.
2. Login: username/password, role, lockout warning after 5 failures.
3. Data Influencer: manual form, niche pills, import preview with valid/skip/invalid rows.
4. Kriteria & Bobot: six SAW criteria, real-time 100% total validation, Likert sub-criteria preview.
5. Perhitungan SAW: three audit stages: Matriks Keputusan, Matriks Normalisasi, Nilai Akhir Vi.
6. Hasil Ranking: top 3 recommendation, score, PDF export preview.

## Content Rules

- Language: Bahasa Indonesia.
- Tone: formal but clear; suitable for academic demo and business review.
- Do not invent unrealistic metrics. If unknown, use honest placeholders or PRD values.
- Preserve SAW terminology exactly: `Matriks Keputusan`, `Matriks Normalisasi`, `Nilai Akhir (Vi)`, `Benefit`, `Cost`, `Bobot`.
- Ranking examples may use realistic names and handles, but keep them clearly prototype data.

## UX Rules

- Admin can create/edit/delete users, criteria, sub-criteria, influencer data, and run SAW.
- Manajer can view dashboard/results and export PDF; results are read-only.
- Total criteria weight must equal `100%`; if not, disable save/count action.
- SAW calculation requires at least 2 active influencers and total weight `100%`.
- Import behavior: valid rows processed, invalid rows flagged, duplicate usernames skipped.
- Export filename format: `Ranking_Influencer_[YYYY-MM-DD].pdf`.

## Responsive Rules

- Breakpoint `1080px`: remove sidebar, show mobile tab nav, stack hero and SAW flow.
- Breakpoint `720px`: stack topbar, grids, and section headers.
- Keep hit targets at least `44px` for buttons and primary inputs on mobile.
- Preserve table readability through horizontal scrolling.

## Avoid

- Purple gradient hero backgrounds.
- Emoji icons.
- Decorative illustrations.
- Fake performance claims like `10x` or `99.9%`.
- Left-border feature cards.
- Heavy shadows or glassmorphism beyond topbar blur.
- Too many accent elements on one screen.

## Current Artifact

- Canonical prototype: `index.html`.
- Title: `SPK Influencer SAW · PT. Behaestex`.
- Identifier: `spk-influencer-saw`.
