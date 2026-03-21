<?php

namespace JobMetric\Attribute\Support;

use Closure;
use Illuminate\Support\Arr;

/**
 * Registry for attribute types (key, name, description, view). Populated from
 * config (attribute.types) at boot; after that the registry is the source of truth.
 *
 * To change a view path during the request lifecycle, use setView() or register()
 * with a merged `view` key. Editing config() alone does not update the registry.
 *
 * To always resolve the path from current config (or any dynamic source) on each
 * getView() call, set `view` to a Closure: fn (string $type) => config(...).
 *
 * @package JobMetric\Attribute
 *
 * @property-read array<string, array> $types Map of type key => options (internal state)
 */
class AttributeTypeRegistry
{
    /**
     * Registered attribute types: type key => options array.
     *
     * @var array<string, array>
     */
    protected array $types = [];

    /**
     * Register an attribute type, or merge options for an existing type.
     *
     * Options typically include:
     * - name: string (translation key, e.g. attribute::base.types.radio.name)
     * - description: string (translation key)
     * - view: string (Blade view name) or Closure(string $type): string
     *
     * @param string $type   Attribute type key (e.g. radio, select).
     * @param array $options name, description, view, etc.
     *
     * @return self
     */
    public function register(string $type, array $options = []): self
    {
        $this->types[$type] = array_merge($this->types[$type] ?? [], $options);

        return $this;
    }

    /**
     * Set or replace the Blade view for a type at runtime (does not require register()).
     * Pass null to remove the view option for that type.
     *
     * @param string               $type Attribute type key.
     * @param string|Closure|null $view Blade view name, or Closure(string $type): string, or null to unset.
     *
     * @return self
     */
    public function setView(string $type, string|Closure|null $view): self
    {
        if (!isset($this->types[$type])) {
            $this->types[$type] = [];
        }

        if ($view === null) {
            unset($this->types[$type]['view']);
        } else {
            $this->types[$type]['view'] = $view;
        }

        return $this;
    }

    /**
     * Remove an attribute type from the registry.
     *
     * @param string $type Type key to remove.
     *
     * @return self
     */
    public function unregister(string $type): self
    {
        unset($this->types[$type]);

        return $this;
    }

    /**
     * Whether a type key is registered.
     *
     * @param string $type Type key.
     *
     * @return bool
     */
    public function has(string $type): bool
    {
        return isset($this->types[$type]);
    }

    /**
     * Options for a registered type.
     *
     * @param string $type Type key.
     *
     * @return array<string, mixed>|null
     */
    public function get(string $type): ?array
    {
        return $this->types[$type] ?? null;
    }

    /**
     * All registered types and options.
     *
     * @return array<string, array>
     */
    public function all(): array
    {
        return $this->types;
    }

    /**
     * Registered type keys only.
     *
     * @return array<int, string>
     */
    public function values(): array
    {
        return array_keys($this->types);
    }

    /**
     * Single option for a type.
     *
     * @param string $type   Type key.
     * @param string $key    Option key.
     * @param mixed $default When missing.
     *
     * @return mixed
     */
    public function getOption(string $type, string $key, mixed $default = null): mixed
    {
        return Arr::get($this->types[$type] ?? [], $key, $default);
    }

    /**
     * Resolved display name for the type (translates when name is a lang key).
     *
     * @param string $type Type key.
     *
     * @return string
     */
    public function getName(string $type): string
    {
        $key = $this->getOption($type, 'name');

        return $key ? (string) __($key) : $type;
    }

    /**
     * Resolved description for the type (translates when description is a lang key).
     *
     * @param string $type Type key.
     *
     * @return string
     */
    public function getDescription(string $type): string
    {
        $key = $this->getOption($type, 'description');

        return $key ? (string) __($key) : '';
    }

    /**
     * Blade view name for rendering this attribute type (or null if unset).
     * If option "view" is a Closure, it is invoked on every call with the type key
     * and must return a string view name (use this to read config() or other dynamic state).
     *
     * @param string $type Type key.
     *
     * @return string|null
     */
    public function getView(string $type): ?string
    {
        $view = $this->getOption($type, 'view');

        if ($view instanceof Closure) {
            $resolved = $view($type);

            return is_string($resolved) ? $resolved : null;
        }

        return is_string($view) ? $view : null;
    }

    /**
     * Remove all registered types.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->types = [];

        return $this;
    }
}
