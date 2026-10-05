# Notes
- Repo = WordPress plugin `faq-manager-pro/` (PHP). Compose runs stock WordPress + MySQL with the plugin bind-mounted (edits apply instantly).
- `setup` one-shot service installs WP (admin / admin-dev-pass, dev only) and seeds demo FAQs + page `/faq-demo/`.
- WP_HOME is derived from BASE44_PUBLIC_HOST_SUFFIX; curl on localhost redirects to the public host — test via the preview.
