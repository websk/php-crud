# Frontend dependency inventory

The npm packages below build the demo assets under `public/assets/libraries`. That directory is ignored by Git and excluded from Composer archives, so applications consuming `websk/php-crud` must provide compatible browser dependencies for the widgets they use.

## npm-managed demo assets

| Package | Version | Runtime use | License | Status |
| --- | --- | --- | --- | --- |
| `bootstrap` | 5.3.8 | Demo layout and CRUD widget markup | MIT | Updated with Bootstrap 5 forms, utilities and JavaScript data attributes. |
| `@popperjs/core` | 2.11.8 | Included in the Bootstrap JavaScript bundle | MIT | Pinned explicitly. |
| `jquery` | 3.7.1 | CRUD widget scripts and legacy plugins | MIT | Updated from 2.2.4. |
| `jquery-ui` | 1.14.2 | Demo layout and autocomplete integrations | MIT | Updated. |
| `jquery-validation` | 1.22.1 | Demo form validation | MIT | Updated. |
| `ace-builds` | 1.45.2 | `CRUDFormWidgetAceTextarea` | BSD-3-Clause | Updated with explicit modes and workers. |
| `@fortawesome/fontawesome-free` | 7.3.1 | CRUD action icons | Font and code licenses | Uses the official v4 compatibility shim for existing class names. |
| `blueimp-file-upload` | 10.32.0 | `CRUDFormWidgetUpload` | MIT | Served from local generated assets instead of a CDN. |
| `medium-editor` | 5.23.3 | `CRUDFormWidgetMediumEditor` | MIT | Served from local generated assets instead of a CDN. |
| `tokenize2` | 1.3.5 | `CRUDTableFilterMultipleAutocomplete` | BSD-3-Clause | Installed directly from the tagged upstream archive and copied by the deterministic build. |

## Project-owned styles

Shared CRUD widget rules live in `resources/css/crud.css` and are generated as
`public/assets/libraries/crud/crud.css`. Consumers must publish and load this file after Bootstrap.

## Removed npm packages

| Package | Removed version | Replacement |
| --- | --- | --- |
| `eonasdan-bootstrap-datetimepicker` | 4.17.49 | Native browser `date` and `datetime-local` controls. |
| `moment` | 2.15.2 | Native browser controls and small value-conversion handlers embedded by the widgets. |
| `font-awesome` | 4.7.0 | `@fortawesome/fontawesome-free` with its v4 compatibility shim. |
| `copy-webpack-plugin` | 8.1.1 | Project-owned deterministic copy script. |
| `webpack` | 5.94.0 | Project-owned deterministic copy script. |
| `webpack-cli` | 4.6.0 | Project-owned deterministic copy script. |
| `@fancyapps/fancybox` | 3.5.7 | Removed because php-crud has no Fancybox integration; popup widgets continue to use the embedded Magnific Popup implementation. |

## Build ownership

The build owns `ace`, `blueimp-file-upload`, `bootstrap`, `crud`, `font-awesome`, `jquery`, `jquery-ui`, `jquery-validation`, `medium-editor`, and `tokenize2` under `public/assets/libraries`. It removes the obsolete `bootstrap-datetimepicker`, `fancybox`, and `moment` directories and fails `check:assets` if they return.

Both the production and complete dependency-tree npm audits report zero known vulnerabilities after the Bootstrap 5 migration.

Run `npm run check` for ESLint and asset verification, `npm run audit:production` for the production moderate threshold, and `npm run audit:high` for the complete high/critical gate.
