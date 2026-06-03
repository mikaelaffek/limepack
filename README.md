# Limepack API MVP

I focused on the smallest version that actually solves the problem. Internal
tools like Manus and Loveable can now access shop data safely, without needing
a developer every time.

The API sits completely outside PrestaShop's core files. Every request goes
through authentication first. Responses are consistent so any tool consuming
it knows exactly what to expect.

I've noted what's not built yet and why: moving keys to DB storage, Bearer
token auth, per-client scopes. These are the right next steps but they're not
needed for a first working version.

It's designed so adding a new resource or a new version is a one line change.
The complexity can grow without the structure needing to change.

The order payload includes Limepack-specific fields pulled directly from your
existing schema, including custom columns already in your data. The order
details response exposes these customer fields: `id_customer`, `firstname`,
`lastname`, `email`, `id_economic`, `id_employee_designer`,
`id_employee_contact`, `id_business_type`, `locations`,
`payment_terms_number`, `ean`, and `invoice_email`.

A thin, versioned REST API for exposing Limepack shop data (and selected
actions) to internal teams and their AI / no-code tools (e.g. Manus,
Loveable) in a safe, consistent, and maintainable way.

Built for the existing **PrestaShop 1.7.7** install, so the code
deliberately avoids PHP 7.4+ syntax (no typed properties, no constructor
promotion).

## Architecture

The module itself stays intentionally thin. Every request flows through a
single gateway front controller and is then handled by small, single-purpose
classes:

```
Request
  -> controllers/front/gateway.php      (single entry point)
     -> classes/Api/Router.php          (picks version + resource)
        -> classes/Api/Controllers/...  (versioned controller: V1/V2)
           -> AuthMiddleware            (validates API key)
           -> Service                   (business logic)
              -> Repository             (DB access)
           -> ApiResponse               (consistent JSON envelope)
```

This layering keeps responsibilities separated: controllers orchestrate,
services hold business logic, repositories touch the database, and the
response/auth concerns are isolated and reusable.

## Files

| File | Responsibility |
| --- | --- |
| `limepackapi.php` | Module entry point. Declares metadata, installs the `moduleRoutes` hook, and exposes routes (delegates generation to `ModuleRoutes`). Kept thin on purpose. |
| `controllers/front/gateway.php` | Single front controller. Wires up the dependencies (provider, middleware, repository, service, controllers) and hands off to the `Router`. |
| `classes/Router/ModuleRoutes.php` | Generates the PrestaShop route definitions for every resource/version. Adding a resource = one line in its `$resources` array. |
| `classes/Api/Router.php` | Dispatches the request to the correct versioned controller (`v1`/`v2`) based on the `version` and `resource` params. |
| `classes/Api/AbstractController.php` | Base controller. Runs the shared lifecycle: authenticate, route by HTTP method, and emit a consistent JSON response (and convert exceptions into JSON errors instead of leaking 500s). |
| `classes/Api/Controllers/V1/OrderController.php` | V1 implementation of the `orders` resource (list + single order). |
| `classes/Api/Controllers/V2/OrderController.php` | V2 placeholder, so the API can evolve without breaking V1 consumers. |
| `classes/Middleware/AuthMiddleware.php` | Authentication gatekeeper. Reads the `key` param and validates it via `ApiClientProvider` before any resource logic runs. |
| `classes/Auth/ApiClientProvider.php` | Source of known API clients/keys (currently hardcoded: `manus`, `loveable`). Designed to later move to DB/Configuration storage. |
| `classes/Service/OrderService.php` | Business logic for orders: builds the order + customer payload (incl. custom fields like `id_economic`, `invoice_email`, etc.). |
| `classes/Service/TrackingService.php` | Business logic for updating an order's tracking number and advancing its status (fires native PS hooks/emails). Prepared for the write/action endpoints. |
| `classes/Repository/OrderRepository.php` | Raw order data access via `DbQuery` (list of orders, paginated). |
| `classes/Response/ApiResponse.php` | Builds the consistent success/error JSON envelope used by every endpoint. |
| `config/routes.yml` | Prepared (commented) Symfony-style routing for the V2 migration. Inert today. |

## Authentication

Every request must include a valid API key (`?key=...`). Keys are defined per
client in `ApiClientProvider`:

- `manus` -> `lp_manus_9f3a1c72d4`
- `loveable` -> `lp_loveable_7b28de91fa`

`AuthMiddleware` runs first inside `AbstractController::handle()`. A missing or
invalid key results in a JSON error response rather than an uncaught fatal.

> Hardening planned: move keys to DB/Configuration with hashed storage, switch
> to an `Authorization: Bearer` header, use `hash_equals()`, and add per-client
> read/write scopes.

## Endpoints

| Method | Path | Description |
| --- | --- | --- |
| GET | `/limepackapi/v1/orders` | List orders (supports `limit`/`offset`). |
| GET | `/limepackapi/v1/orders/{id}` | Single order with customer + product detail. |

Example:

```
GET /limepackapi/v1/orders/1?key=lp_manus_9f3a1c72d4
```

Success envelope:

```json
{ "success": true, "data": { "id_order": 1, "reference": "XKBKNABJK", "...": "..." } }
```

Error envelope:

```json
{ "success": false, "error": { "code": 401, "message": "Invalid API key" } }
```

## Routing

Currently uses `hookModuleRoutes()` for simplicity. Route generation is
centralized in `classes/Router/ModuleRoutes.php`, so adding a new API
resource is a one-line change.

Next step: migrate to Symfony `routes.yml`, following the modern PrestaShop 8
routing pattern (already used by the Artworks module in
`modules/artworks/config/routes.yml`) — gives per-route HTTP method
enforcement and direct controller-action mapping. See `config/routes.yml` for
the prepared (commented) configuration.