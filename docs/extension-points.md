# Extension points reference

The [overview](overview.md) is the complete 28-type contribution map and the
registrar inventory. This page gives the short implementation recipes used by
the active examples.

The runnable source is available in the public [Extension Cookbook repository](https://github.com/capell-app/extension-cookbook/tree/4.x). The links below point
to the owning provider, bridge, data boundary, and tests so the prose can be
checked against the implementation.

## Public frontend

`FrontendServiceProvider` runs only when the package is installed and the
Frontend provider bucket is active. It owns the package route and view
namespace, registers the typed widget definition, tags the frontend component
contributor, registers the CSS and JavaScript resource group, and contributes
the public render hook.

`ExtensionCookbookServiceProvider` is the gated runtime provider. It owns
Core surface registration, the doctor command and health schedule in console
contexts, and explicit content-graph extractor registration.

See the [runtime provider](https://github.com/capell-app/extension-cookbook/blob/4.x/src/Providers/ExtensionCookbookServiceProvider.php#L30-L72)
and [frontend provider](https://github.com/capell-app/extension-cookbook/blob/4.x/src/Providers/FrontendServiceProvider.php#L25-L91).

The widget definition is keyed as `capell-app.extension-cookbook` and requires
all four typed boundaries: `ExtensionCookbookWidget`, `ExtensionCookbookWidgetInputData`,
`ExtensionCookbookWidgetRenderData`, and the package fallback view. Its resource group
is `capell-app.extension-cookbook`, so the widget's assets are selected by the
normal Layout Builder resource plan.

`ExtensionCookbookFrontendComponentContributor` implements the public
`FrontendComponentContributor` contract and returns a Blade target with the
package view identifier. `FrontendHookRegistrar::contribute()` registers a
stable owner and key at `BodyEnd`, targeted to this route. The hook renders a
plain paragraph and has no package selector, model identity, authoring state,
or editor URL.

The corresponding [typed widget](https://github.com/capell-app/extension-cookbook/blob/4.x/src/Widget/ExtensionCookbookWidget.php)
and [frontend safety tests](https://github.com/capell-app/extension-cookbook/blob/4.x/tests/Feature/Frontend/ExtensionCookbookFrontendTest.php)
show the boundary end to end.

The resource group uses typed `FrontendResourceData` values and local public
resource paths. The package's normal asset publication configuration owns
making `resources/dist/extension-cookbook.css` and
`resources/dist/extension-cookbook.js` available below the declared public
paths. The stylesheet only contains reduced-motion protection. The script is
progressive enhancement and is not required for the page content.

## Health, command, job, and graph

`ExtensionCookbookHealthCheck::runDiagnostics()` checks only package-owned
storage. It reports a typed `DoctorCheckResultData` collection and performs no
writes. `ExtensionCookbookDoctorCommand` evaluates that collection once,
prints each result, and returns the corresponding Symfony success or failure
code. The scheduled `AuditExtensionCookbookHealthJob` invokes the same check
without mutating content.

`ReferenceEntryContentGraphExtractor` implements the Core
`ContentGraphExtractor` contract. It returns an empty typed collection for a
non-entry, an entry without `site_id` or `related_page_id`, or a related Page
from another site; otherwise it emits one weak, directed relation to the Core
Page identity. It checks persisted Page site state without lazy-loading the
`relatedPage` relationship.

The [content-graph test](https://github.com/capell-app/extension-cookbook/blob/4.x/tests/Feature/ContentGraph/ReferenceEntryContentGraphTest.php)
keeps the site-ownership guard executable.

## Deliberately inactive surfaces

Manifest marker classes remain for all 27 current contribution types so the
manifest is auditable. Types without a corresponding public runtime registrar
in this package are explicitly inactive in the overview: sections, page
variations, and agent capabilities. Workflow attention is a demonstrated
conditional contribution that emits an item only with its supported actor and
context. The package also
avoids broad admin replacement seams and other registrar methods that would
imply ownership of unrelated screens.

The package does not introduce receipt, ordering, public render-data, or stable
Admin-zone APIs. Core's public render-data contributor is currently
experimental and absent from this package's released dependency contract; it is
tracked as a follow-up rather than presented as a supported cookbook recipe.
