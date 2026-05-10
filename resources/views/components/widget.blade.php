@php
    $widgetPackageData = isset($data) && is_array($data) ? $data : array();
    $widgetPackageOutput = isset($output) ? $output : widget_package_render($alias, $widgetPackageData);
@endphp

{{ $widgetPackageOutput }}
