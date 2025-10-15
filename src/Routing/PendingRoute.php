<?php

namespace HMsoft\Cms\Routing;

/**
 * Represents a route that has been defined but not yet registered.
 * It now captures all chained method calls like ->defaults() or ->where().
 *
 * يمثل مسارًا تم تعريفه ولكن لم يتم تسجيله بعد.
 * يقوم الآن بالتقاط كل استدعاءات الدوال المتسلسلة مثل ->defaults() أو ->where().
 */
class PendingRoute
{
    /**
     * Stores any chained method calls (e.g., defaults, where).
     * @var array
     */
    protected array $chainedCalls = [];

    public function __construct(
        protected string $method,
        protected string $uri,
        protected $action,
        protected array &$routesCollection
    ) {}

    /**
     * The final method in the chain that registers the route definition.
     * الدالة النهائية في السلسلة التي تسجل تعريف المسار.
     */
    public function name(string $name): self
    {
        $this->routesCollection[] = [
            'method' => $this->method,
            'uri' => $this->uri,
            'action' => $this->action,
            'name' => $name,
            'chained' => $this->chainedCalls, // Store the chained calls
        ];
        return $this;
    }

    /**
     * Magically handle any other method calls (like defaults, where, etc.).
     * التعامل مع أي استدعاءات دوال أخرى بشكل سحري.
     */
    public function __call(string $method, array $parameters): self
    {
        $this->chainedCalls[] = ['method' => $method, 'parameters' => $parameters];
        return $this;
    }
}
