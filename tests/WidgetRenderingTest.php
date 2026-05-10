<?php

namespace Highvertical\WidgetPackage\Tests;

use Highvertical\WidgetPackage\Exceptions\InvalidWidgetException;
use Highvertical\WidgetPackage\Exceptions\InvalidWidgetOutputException;
use Highvertical\WidgetPackage\Exceptions\WidgetNotFoundException;
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
        widget_package_register('plain-text', PlainTextWidget::class);

        $output = widgetPackage('plain-text', array(
            'content' => '<script>alert("x")</script>',
        ));

        $this->assertSame('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', (string) $output);
    }

    public function test_it_allows_explicit_raw_html_via_html_string(): void
    {
        widget_package_register('html-widget', HtmlWidget::class);

        $output = widgetPackage('html-widget');

        $this->assertSame('<strong>Safe HTML</strong>', (string) $output);
    }

    public function test_it_keeps_the_legacy_widget_helper_available_in_1x(): void
    {
        widget_package_register('plain-text', PlainTextWidget::class);

        $output = widget('plain-text', array(
            'content' => 'Legacy helper',
        ));

        $this->assertSame('Legacy helper', (string) $output);
    }

    public function test_it_renders_widgets_through_the_component_alias(): void
    {
        widget_package_register('view-widget', ViewWidget::class);

        $output = view('widget-package-tests::host-component', array(
            'name' => 'Taylor',
        ))->render();

        $this->assertStringContainsString('Hello, Taylor!', $output);
    }

    public function test_it_renders_widgets_through_the_include_alias(): void
    {
        widget_package_register('view-widget', ViewWidget::class);

        $output = view('widget-package-tests::host-include', array(
            'name' => 'Taylor',
        ))->render();

        $this->assertStringContainsString('Hello, Taylor!', $output);
    }

    public function test_it_renders_widgets_through_the_legacy_directive(): void
    {
        widget_package_register('view-widget', ViewWidget::class);

        $output = view('widget-package-tests::host-directive', array(
            'name' => 'Taylor',
        ))->render();

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

        widget_package_register('invalid-output', InvalidOutputWidget::class);

        widgetPackage('invalid-output');
    }
}
