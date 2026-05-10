# Highvertical Widget Package

Blade-first widgets for Laravel 7, 8, and 9.

This v1 line is intentionally conservative: it stays compatible with older Laravel applications, avoids automatic host-project mutations, and keeps rendering escaped by default.

## Compatibility

| Package line | Laravel | PHP | Status |
| --- | --- | --- | --- |
| `1.x` | `^7.0 \| ^8.0 \| ^9.0` | `^7.2.5 \| ^8.0` | Current stable line |
| `2.x` | Laravel 10+ | TBA | Planned |

Laravel 10+ users should wait for the `2.x` line when it is released.

## Safety

This package does not modify your application unless you explicitly publish resources.

It does not:

- write files into your application during normal boot
- clear caches, routes, config, or compiled views
- run migrations automatically
- register middleware
- make HTTP requests
- scan your filesystem or modules on each request
- inject remote assets, telemetry, or tracking

## Installation

```bash
composer require highvertical/widget-package
```

Laravel package discovery is enabled by default. If you need manual registration, add the service provider to `config/app.php`:

```php
'providers' => [
    Highvertical\WidgetPackage\Providers\WidgetServiceProvider::class,
],
```

## Optional Publishing

Publish the config file:

```bash
php artisan vendor:publish --tag=widget-package-config
```

This creates:

```text
config/widget-package.php
```

Legacy `1.x` applications that still expect `config/widgets.php` may continue using:

```bash
php artisan vendor:publish --tag=widget-config
```

Publish the package views if you want to customize the built-in component template:

```bash
php artisan vendor:publish --tag=widget-package-views
```

This creates:

```text
resources/views/vendor/widget-package/components/widget.blade.php
```

The package does not currently ship assets or migrations, so there are no `widget-package-assets` or `widget-package-migrations` publish groups in `1.x`.

## Configuration

Publishing is optional. The package works without it.

Preferred config:

```php
<?php

return [
    'widgets' => [
        // 'profile-card' => \App\Widgets\ProfileCardWidget::class,
    ],
];
```

## Creating a Widget

Create a widget class that extends `Highvertical\WidgetPackage\Widgets\Widget`:

```php
<?php

namespace App\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;

class WelcomeWidget extends Widget
{
    public function render(array $params = array())
    {
        return view('widgets.welcome', array(
            'name' => isset($params['name']) ? $params['name'] : 'Guest',
        ));
    }
}
```

Register it in `config/widget-package.php`:

```php
'widgets' => [
    'welcome' => \App\Widgets\WelcomeWidget::class,
],
```

## Basic Usage

### Blade component

```blade
<x-widget-package alias="welcome" :data="['name' => 'Taylor']" />
```

### Blade include alias

```blade
@widgetPackage(['alias' => 'welcome', 'data' => ['name' => 'Taylor']])
```

### Legacy directive

The original `@widget` directive remains available in `1.x` for backward compatibility:

```blade
@widget('welcome', ['name' => 'Taylor'])
```

### Helper API

```php
echo widgetPackage('welcome', array('name' => 'Taylor'));
```

### Programmatic registration

You can also register widgets in your own service provider:

```php
use Highvertical\WidgetPackage\WidgetManager;

public function boot()
{
    app(WidgetManager::class)->register('welcome', \App\Widgets\WelcomeWidget::class);
}
```

The legacy `widget()` and `register_widget()` helpers are still available in `1.x`, but the Blade component and `widgetPackage()` helper are the preferred APIs for new code.

## Advanced Usage

### Constructor dependencies

Widget classes are resolved through Laravel's container, so dependencies may be injected normally:

```php
<?php

namespace App\Widgets;

use App\Services\ProfileService;
use Highvertical\WidgetPackage\Widgets\Widget;

class ProfileWidget extends Widget
{
    /**
     * @var \App\Services\ProfileService
     */
    protected $profiles;

    public function __construct(ProfileService $profiles)
    {
        $this->profiles = $profiles;
    }

    public function render(array $params = array())
    {
        return view('widgets.profile', array(
            'profile' => $this->profiles->find(isset($params['user_id']) ? $params['user_id'] : null),
        ));
    }
}
```

### Escaped output by default

If your widget returns a plain string, the package escapes it before rendering. This is the default safe path.

### Explicit raw HTML

If you intentionally want raw HTML output, return an `Illuminate\Support\HtmlString`:

```php
use Illuminate\Support\HtmlString;

public function render(array $params = array())
{
    return new HtmlString('<strong>Trusted markup</strong>');
}
```

Use this sparingly and only with trusted content.

### Overriding the package view

After publishing views, you may customize the wrapper used by:

```blade
<x-widget-package ... />
```

Override file:

```text
resources/views/vendor/widget-package/components/widget.blade.php
```

## Troubleshooting

### Widget not found

Make sure the alias exists in `config/widget-package.php` or is registered programmatically before rendering.

### Widget class rejected

Widget classes must extend `Highvertical\WidgetPackage\Widgets\Widget`.

### Raw HTML is escaped

Return an `HtmlString` or a Blade view if you truly want HTML output. Plain strings are escaped intentionally.

### Published config changes are not visible

Run:

```bash
php artisan config:clear
```

only if your application has cached configuration. The package itself never clears config automatically.

## Testing

Run the package test suite with:

```bash
composer test
```

The repository includes an Orchestra Testbench setup for Laravel 7, 8, and 9 compatibility work.

## CI

GitHub Actions is configured with a Laravel/PHP matrix covering the supported `1.x` line. If any dependency combination proves unresolvable in your environment, remove only that matrix entry and document the reason in the workflow or release notes.

## Contributing

1. Fork the repository.
2. Install dependencies with Composer.
3. Run `composer test`.
4. Submit a pull request with focused changes and clear compatibility notes.

## Upgrade Path

- Laravel 7, 8, and 9 projects should remain on `1.x`.
- Laravel 10+ projects should adopt `2.x` when that line is released.
- `config/widget-package.php` is the preferred config file in `1.2.0`; `config/widgets.php` remains supported as a legacy alias for backward compatibility.
- Because this repository already contains a `1.1.3` version marker, the safest next stable tag for this work is a later `1.x` release, not a new `1.0.0` tag.

## License

MIT
