# Widget Package for Laravel

## Introduction

The Widget Package is a simple yet powerful Laravel package that allows developers to create reusable widgets for their applications. These widgets can be used anywhere in Blade views, making it easier to manage complex UIs, build dynamic content, and maintain a clean separation of concerns. The package is designed to be compatible with both small and large projects, including modular applications like those built with nwidart/laravel-modules.

## Features

#### Simple Widget Creation:
Create widgets that can be easily rendered in Blade views.

#### Caching: 
Built-in support for caching widget output to improve performance.

#### Dynamic Configuration: 
Define and manage widgets via a configuration file.

#### Modular Compatibility: 
Fully compatible with modular applications, including those using nwidart/laravel-modules.

#### Advanced Blade Directives: 
Use custom Blade directives to render widgets anywhere in your views.

## Installation

- Step 1: Install the Package
Install the package via Composer:

```bash
composer require highvertical/widget-package
```

- Step 2: Publish the Configuration File
Publish the package's configuration file to your Laravel application's config directory:

```bash
php artisan vendor:publish --tag=widget-config
```

This command will create a config/widgets.php file in your application.

- Step 3: Autoload the Service Provider (Optional)
If your application does not support package auto-discovery, add the service provider manually in config/app.php:

```bash
'providers' => [
    // Other Service Providers...

    Highvertical\WidgetPackage\Providers\WidgetServiceProvider::class,
],
```

## Basic Usage

- Step 1: Create a Widget
Widgets can be created anywhere in your application. Here's an example of a simple weather widget:

```bash
<?php

namespace App\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;

class WeatherWidget extends Widget
{
    public function render(array $params = [])
    {
        $location = $params['location'] ?? 'Unknown Location';

        // Mocked data, replace with real data fetching logic
        $weatherData = [
            'location' => $location,
            'temperature' => '25°C',
            'condition' => 'Sunny',
        ];

        return view('widgets.weather', compact('weatherData'));
    }
}
```

- Step 2: Register the Widget
After creating the widget class, register it in your config/widgets.php file:

```bash
<?php

return [
    'widgets' => [
        'weather' => \App\Widgets\WeatherWidget::class,
    ],
    'cache' => [
        'enabled' => true,
        'ttl' => 60, // Cache time-to-live in minutes
    ],
];
```

- Step 3: Use the Widget in a Blade View
You can now use your widget in any Blade view with the custom @widget directive:

```bash
@widget('weather', ['location' => 'New York'])
```

## Advanced Usage

### Caching
The Widget Package supports caching out of the box. To enable caching, simply ensure that the cache.enabled configuration is set to true in your config/widgets.php file:

```bash
'cache' => [
    'enabled' => true,
    'ttl' => 60, // Cache time-to-live in minutes
],
```

When caching is enabled, the widget output is stored and reused for subsequent requests within the specified time-to-live (TTL) period.

### Dynamic Widget Configuration

Widgets can be defined dynamically across different parts of your application, including within modular applications. This allows for more flexible and maintainable code, particularly in larger projects.

Example: Modular Widgets with nwidart/laravel-modules

If you are using nwidart/laravel-modules, you can define widgets within individual modules. For example, in a module named Blog, create a Config/widgets.php file:

```bash
<?php

return [
    'widgets' => [
        'recentPosts' => \Modules\Blog\Widgets\RecentPostsWidget::class,
    ],
];
```

The package will automatically detect and register these widgets, making them available across your entire application.

### Handling Widget Dependencies

Widgets can have dependencies that need to be resolved by Laravel's service container. You can easily inject these dependencies by defining a constructor in your widget class:

```bash
<?php

namespace App\Widgets;

use App\Services\WeatherService;
use Highvertical\WidgetPackage\Widgets\Widget;

class WeatherWidget extends Widget
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function render(array $params = [])
    {
        $location = $params['location'] ?? 'Unknown Location';
        $weatherData = $this->weatherService->getWeather($location);

        return view('widgets.weather', compact('weatherData'));
    }
}
```

### Extending and Overriding Configuration
To extend or override the default widget configuration, modify your config/widgets.php file. This is particularly useful if you are integrating the package into an existing project and need to adapt it to your specific needs.

### Blade Directives
The package registers a custom Blade directive @widget to make it easy to render widgets in your views. This directive takes the widget alias and an optional array of parameters.

```bash
@widget('weather', ['location' => 'San Francisco'])
```

## Example Widgets

- Example 1: Recent Posts Widget

```bash
<?php

namespace Modules\Blog\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;
use Modules\Blog\Repositories\PostRepository;

class RecentPostsWidget extends Widget
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function render(array $params = [])
    {
        $posts = $this->postRepository->getRecentPosts($params['limit'] ?? 5);

        return view('blog::widgets.recent-posts', compact('posts'));
    }
}
```

- Example 2: User Profile Widget

```bash
<?php

namespace App\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;
use App\Models\User;

class UserProfileWidget extends Widget
{
    public function render(array $params = [])
    {
        $user = User::find($params['user_id']);

        return view('widgets.user-profile', compact('user'));
    }
}
```

## Customization and Extensibility

The Widget Package is designed to be easily customizable and extensible. You can create your own widgets, customize existing ones, or even extend the package's core functionality to meet your specific needs.

## Contributing

We welcome contributions to the Widget Package. If you have ideas for improvements, find a bug, or want to help with the documentation, feel free to submit a pull request or open an issue on GitHub.

## License

The Widget Package is open-sourced software licensed under the MIT license.