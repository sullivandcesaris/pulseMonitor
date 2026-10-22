# Frontend Architecture

PulseMonitor uses Vue 3 with the Composition API, TypeScript, Inertia, and Vite. Laravel owns routing and server-side authorization; Inertia transports page props to Vue pages without introducing a separate API layer for the web application.

## Source layout

```text
resources/js/
├── components/      reusable UI and shadcn-vue primitives
├── composables/     reusable Vue state and behavior
├── layouts/         authenticated, settings, and authentication shells
├── lib/             frontend utilities
├── pages/           route-level Inertia pages
└── types/           shared TypeScript contracts
```

## Conventions

- Route-level screens belong in `resources/js/pages` and are named after their Inertia page path.
- Shared presentation belongs in `resources/js/components`; generic primitives belong in `components/ui`.
- Use `<script setup lang="ts">` and Composition API patterns for new Vue components.
- Use Inertia `Link`, `Head`, and `useForm` for navigation, document titles, and server-backed forms.
- Keep organization and authorization decisions on the Laravel side; frontend visibility is not a security boundary.
- Prefer design tokens from `resources/css/app.css` over one-off colors.
- Use the shared light/dark appearance composable instead of component-local theme state.

## Design system

Tailwind CSS is configured with CSS variables for background, foreground, card, primary, muted, border, ring, chart, and sidebar tokens. The generated shadcn-vue primitives in `components/ui` provide consistent buttons, cards, forms, dialogs, menus, and navigation patterns.

The application shell uses a responsive sidebar layout on desktop and the starter kit's sheet-based navigation on smaller screens. Dashboard pages should use the same spacing, card, typography, and status-badge conventions established in `Dashboard.vue`.

## Quality checks

```bash
npm run build
npm run lint
npm run format:check
npm exec vue-tsc -- --noEmit
```
