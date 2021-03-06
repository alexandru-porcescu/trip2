@php

$route = $route ?? '';
$title = $title ?? '';
$icon = isset($icon) ? $icon : 'icon-arrow-right';

@endphp

<a href="{{ $route }}">
	
    <div class="Link {{ $isclasses }}">
        
	    <div class="Link__title">
	      
            {{ $title }}
	        
	    </div>

        @if ($icon)
            <div class="Link__icon">

                {!! component('Icon')->is('gray')->with('icon', $icon) !!}

            </div>
        @endif

	</div>

</a>
