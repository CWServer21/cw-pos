@props([
    'title' => 'Info',
    'type' => 'primary',
    'size' => 'regular' // options: 'small', 'regular'
])

@php
    $iconClass = $size === 'small' ? 'info-icon-sm' : 'info-icon';
@endphp

<span class="dripicons-information" data-toggle="tooltip" title="{{ $title }}" style="background-color:#cce5ff;border-radius:50%;color:#004085;display:inline-block;font-size: 15px;height:16px;line-height:1px"></span>
