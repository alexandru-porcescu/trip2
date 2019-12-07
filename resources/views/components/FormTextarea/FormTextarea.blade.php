@php

$title = $title ?? '';
$name = $name ?? '';
$value = $value ?? '';
$rows = $rows ?? 8;
$cols = $cols ?? 50;
$placeholder = $placeholder ?? '';
$disabled = $disabled ?? false;

@endphp

<div class="FormTextarea {{ $isclasses }} {{ $errors->first($name) ? 'FormTextfield--error' : ''}}">

    @if ($title)

        <label for="{{ $name }}" class="FormTextarea__label">{{ $title }}</label>
    
    @endif

    <textarea
        class="FormTextarea__textarea"
        id={{ $name }}
        name="{{ $name }}"
        rows="{{ $rows }}"
        cols="{{ $cols }} "
        placeholder="{{ $placeholder }}"
        dusk="{{ slug($title) }}"
        @if($disabled)
          disabled
        @endif
    >{{ $value }}</textarea>

</div>