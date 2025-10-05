<?php

namespace HMsoft\Cms\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful as SanctumMiddleware;

/**
 * This middleware extends Sanctum's default behavior to differentiate between
 * stateful frontend applications (like web dashboards) and stateless API clients
 * (like SPAs or mobile apps) that might be served from the same domain.
 *
 * It inspects a custom HTTP header, 'X-Client-Application', to explicitly
 * identify stateless clients, bypassing Sanctum's stateful checks for them.
 */
class CustomEnsureFrontendRequestsAreStateful extends SanctumMiddleware
{
    /**
     * A list of client identifiers that should always be treated as stateless.
     * These clients are expected to authenticate using API tokens (e.g., Bearer tokens).
     * The identifiers are sent via the 'X-Client-Application' HTTP header.
     * Loaded from STATELESS_CLIENTS environment variable.
     *
     * @var array
     */
    protected $statelessClients;

    /**
     * Constructor to initialize stateless clients from environment.
     */
    public function __construct()
    {
        $this->statelessClients = explode(',', env('STATELESS_CLIENTS', 'hPanel-React'));
    }

    /**
     * Handle an incoming request.
     *
     * This method intercepts the request before it's processed by Sanctum's default
     * stateful logic. It checks for the presence and validity of a custom client
     * identifier to decide whether the request should be treated as stateless.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next): Response
    {
        // Retrieve the custom client identifier from the request headers.
        $clientIdentifier = $request->header('X-Client-Application');

        // Check if the provided client identifier is in our list of known stateless clients.
        if ($clientIdentifier && in_array($clientIdentifier, $this->statelessClients)) {
            // If it is a known stateless client, bypass Sanctum's stateful checks
            // and pass the request to the next middleware in the stack.
            // This forces the request to be authenticated via API token.
            return $next($request);
        }

        // If the header is not present or the client is not in the stateless list,
        // fall back to Sanctum's default behavior. The parent's handle() method
        // will check the request's origin against the `stateful_domains` config
        // to determine if it should be treated as a stateful request.
        return parent::handle($request, $next);
    }
}
