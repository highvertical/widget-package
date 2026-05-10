<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\Tests;

use Highvertical\WidgetPackage\Exceptions\InvalidWidgetException;
use Highvertical\WidgetPackage\Exceptions\InvalidWidgetOutputException;
use Highvertical\WidgetPackage\Exceptions\WidgetNotFoundException;
use Highvertical\WidgetPackage\Tests\Fixtures\Widgets\HtmlableWidget;
use Highvertical\WidgetPackage\Tests\Fixtures\Widgets\HtmlWidget;
use Highvertical\WidgetPackage\Tests\Fixtures\Widgets\InvalidOutputWidget;
use Highvertical\WidgetPackage\Tests\Fixtures\Widgets\PlainTextWidget;
use Highvertical\WidgetPackage\Tests\Fixtures\Widgets\ViewWidget;
use Highvertical\WidgetPackage\WidgetManager;
use stdClass;

class WidgetRenderingTest extends TestCase
{
    public function test_it_renders_plain_text_widgets_as_escaped_output_with_the_preferred_helper(): void
    {
        $this->app->make(WidgetManager::class)->register('plain-text', PlainTextWidget::class);

        $output = widgetPackage('plain-text', [
            'content' => '<script>alert("x")</script>',
        ]);

        $this->assertSame('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', (string) $output);
    }

    public function test_it_allows_explicit_raw_html_via_html_string(): void
    {
        $this->app->make(WidgetManager::class)->register('html-widget', HtmlWidget::class);

        $output = widgetPackage('html-widget');

        $this->assertSame('<strong>Safe HTML</strong>', (string) $output);
    }

    public function test_it_rejects_custom_htmlable_objects_to_keep_raw_html_explicit(): void
    {
        $this->expectException(InvalidWidgetOutputException::class);

        $this->app->make(WidgetManager::class)->register('htmlable-widget', HtmlableWidget::class);

        widgetPackage('htmlable-widget');
    }

    public function test_it_renders_widgets_through_the_component_alias(): void
    {
        $this->app->make(WidgetManager::class)->register('view-widget', ViewWidget::class);

        $output = view('widget-package-tests::host-component', [
            'name' => 'Taylor',
        ])->render();

        $this->assertStringContainsString('Hello, Taylor!', $output);
    }

    public function test_it_renders_widgets_through_the_include_alias(): void
    {
        $this->app->make(WidgetManager::class)->register('view-widget', ViewWidget::class);

        $output = view('widget-package-tests::host-include', [
            'name' => 'Taylor',
        ])->render();

        $this->assertStringContainsString('Hello, Taylor!', $output);
    }

    public function test_it_keeps_the_legacy_blade_directive_available_for_view_continuity(): void
    {
        $this->app->make(WidgetManager::class)->register('view-widget', ViewWidget::class);

        $output = view('widget-package-tests::host-directive', [
            'name' => 'Taylor',
        ])->render();

        $this->assertStringContainsString('Hello, Taylor!', $output);
    }

    public function test_it_throws_a_clear_exception_for_unknown_widgets(): void
    {
        $this->expectException(WidgetNotFoundException::class);

        widgetPackage('missing-widget');
    }

    public function test_it_rejects_invalid_widget_classes(): void
    {
        $this->expectException(InvalidWidgetException::class);

        $this->app->make(WidgetManager::class)->register('invalid', stdClass::class);
    }

    public function test_it_rejects_invalid_widget_output_payloads(): void
    {
        $this->expectException(InvalidWidgetOutputException::class);

        $this->app->make(WidgetManager::class)->register('invalid-output', InvalidOutputWidget::class);

        widgetPackage('invalid-output');
    }

    public function test_v1_legacy_global_helpers_are_removed_in_v2(): void
    {
        $this->assertFalse(function_exists('widget'));
        $this->assertFalse(function_exists('register_widget'));
        $this->assertFalse(function_exists('widget_package_register'));
        $this->assertFalse(function_exists('widget_package_render'));
        $this->assertFalse(function_exists('widget_package_manager'));
    }
}
