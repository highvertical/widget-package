@php
    $widgetPackageData = isset($data) && is_array($data) ? $data : [];
    $widgetPackageOutput = isset($output) ? $output : widgetPackage($alias, $widgetPackageData);
@endphp

{{ $widgetPackageOutput }}
