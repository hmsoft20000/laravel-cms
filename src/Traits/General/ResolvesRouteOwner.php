<?php

namespace HMsoft\Cms\Traits\General;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait ResolvesRouteOwner
 *
 * This trait provides a robust way for generic controllers to resolve the
 * parent 'owner' model from a route, especially when custom binding names are used.
 * It leverages the smart 'owner' resolver defined in the RouteServiceProvider.
 */
trait ResolvesRouteOwner
{
    /**
     * Intelligently resolves the owner model from the current request's route.
     * This method should be called inside controller actions that need the owner.
     */
    protected function resolveOwner($request): Model|null
    {

        $owner = null;

        // /** @var Route $route */
        $route = $request->route();

        /** @var \Illuminate\Database\Eloquent\Model $owner */
        // $owner = $request->route('owner');
        $owner = $request->route('owner');
        $ownerKey = $route->parameter('_owner_binding_key');
        // info([
        //     'owner' => $owner,
        //     'ownerKey' => $ownerKey,
        // ]);
        if (is_null($owner)) {

            $binder = app('router')->getBindingCallback($ownerKey);
            // info([
            //     'binder' => $binder,
            // ]);
            if ($binder) {
                $value = $route->originalParameter($ownerKey);
                $owner = $binder($value, $route);
            }
        }


        // // This relies on the "master" owner binding you created in your RouteServiceProvider.
        // // It's the central, intelligent piece of the puzzle.
        // $owner = $this->resolveRouteBinding($request, 'owner');

        // if (!$owner instanceof Model) {
        //     // This is a safeguard in case the binding fails for any reason.
        //     abort(404, 'Owner model could not be resolved for this route.');
        // }

        return $owner;
    }

    protected function resolveRouteParameter($request, string $parameterName): mixed
    {
        $value = $request->route()->parameter($parameterName);
        if (is_string($value)) {
            throw new \Exception('binding ' . $parameterName . ' failed');
        }
        return $value;
    }

    /**
     * Manually trigger Laravel's route binding resolution for a specific parameter.
     * We do this because we've removed the Model type-hint from the controller method
     * to avoid implicit binding conflicts.
     */
    // private function resolveRouteBinding(Request $request, string $parameterName): mixed
    // {
    //     // This is a safe way to ask Laravel to run its binding logic for a specific key.
    //     return $request->route()->resolveParameter($parameterName);
    // }
}
