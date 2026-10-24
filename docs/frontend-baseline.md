# Frontend behavior baseline

This baseline records the behavior that must remain available when the demo frontend or consumer-provided assets change.

| Area | Expected behavior |
| --- | --- |
| CRUD forms | Required fields are highlighted and invalid forms are not submitted. |
| Date widget | Shows a native date control and submits `YYYY-MM-DD`. |
| Date-time widget | Shows a native date-time control and submits `YYYY-MM-DD HH:mm:ss`. |
| Timestamp widget | Shows a native date-time control and submits a Unix timestamp in the browser's local time zone. |
| Nullable date widgets | Selecting “Нет значения” clears the visible and submitted values; entering a value clears that checkbox. |
| Disabled timestamp widget | Displays the formatted value and preserves the original hidden timestamp on submission. |
| Ace textarea | Loads Ace, supports the declared HTML, JavaScript, JSON, PHP, SQL, XML and text modes, and synchronizes edits into the submitted textarea. |
| Reference and autocomplete widgets | Search results can be selected and cleared. |
| Multiple autocomplete filter | Tokenize2 initializes and adds configured values. |
| Popup forms | Magnific Popup opens inline create and reference forms. |
| Demo layout | jQuery, jQuery UI, Bootstrap, validation, shared CRUD styles and the Font Awesome compatibility styles load from `/assets/libraries`. |

The demo assets are generated locally and are excluded from the Composer archive. Library consumers must load compatible assets at the paths expected by the widgets or adapt those paths in their application integration.

The Playwright regression renders the PHP widgets through a local PHP server and verifies every row in this baseline in a real Chromium browser. Local test endpoints exercise reference selection and remote autocomplete without external network dependencies.
