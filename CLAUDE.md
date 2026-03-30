# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a static personal portfolio/CV website for Sean Grimes. No build system, no package manager — files are deployed directly to a web server.

## Local Development

Serve the files with any HTTP server:
```bash
python3 -m http.server 8000
# or, to also test PHP backend:
php -S localhost:8000
```

## Architecture

Three main pages, each with its own CSS file:
- `index.html` + `style.css` — Primary CV page (profile, work experience, education, publications)
- `teaching.html` + `teaching.css` — Teaching portfolio with Chart.js rating visualizations
- `support.html` + `support.css` — Contact/support form with humorous theme

**Backend:** `support.php` handles POST submissions from the support form. It validates/sanitizes input, then appends JSON lines to `support_submissions.txt`. No database — file-based storage only.

**External dependencies (CDN only):**
- Google Fonts (Source Serif 4, Work Sans, Space Grotesk, IBM Plex Mono)
- Chart.js 4.4.1 (used in `teaching.html` for course rating charts)

## Key Patterns

- CSS uses custom properties (variables) defined at `:root` for colors/typography
- Responsive breakpoints at 900px and 600px throughout all CSS files
- The support form uses `localStorage` as a backup before POSTing to `support.php`
- `syllabi/` contains PDFs named `<course>-latest.pdf` — update the file, keep the same name
