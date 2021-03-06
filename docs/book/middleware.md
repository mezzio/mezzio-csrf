# CSRF Guard Middleware

Since CSRF token generation and validation relies on request artifacts, we
provide `Mezzio\Csrf\CsrfMiddleware` to generate the appropriate guard
instance and pass it into a request attribute.

This approach allows you to have a single location or specific locations where
CSRF guards are generated, which can then be used by any middleware in your
application.

The `CsrfMiddleware` has the following constructor arguments:

- `CsrfGuardFactoryInterface $guardFactory`: a concrete instance to use for
  generating the CSRF guard instance.
- `string $attributeKey`: the name of the request attribute in which to store
  the CSRF guard instance. Defaults to the `CsrfMiddleware::GUARD_ATTRIBUTE`
  ("csrf").

We provide and map a factory for the middleware,
`Mezzio\Csrf\CsrfMiddlewareFactory`; that factory depends on having the
service `Mezzio\Csrf\CsrfGuardFactoryInterface` defined (by default it
is, and points to the `SessionCsrfGuard` implementation).

If you want to override the defaults, create and map a custom factory.

## Registering the middleware

The middleware depends on the `Mezzio\Session\SessionMiddleware`, and
must be piped **AFTER** that middleware. It can be piped either in the
application pipeline, or within routed middleware.

As an example, in `config/pipeline.php`:

```php
$app->pipe(\Mezzio\Session\SessionMiddleware::class);
$app->pipe(\Mezzio\Csrf\CsrfMiddleware::class);
```

Within routed middleware:

```php
$app->get('/user/login', [
    \Mezzio\Session\SessionMiddleware::class,
    \Mezzio\Csrf\CsrfMiddleware::class,
    UserLoginFormHandler::class,
]);

$app->post('/user/login', [
    \Mezzio\Session\SessionMiddleware::class,
    \Mezzio\Csrf\CsrfMiddleware::class,
    ProcessUserLoginHandler::class,
]);
```
