@if($showLabel && $showField)
    @if($options['wrapper'] !== false)
        <div {!! $options['wrapperAttrs'] !!}>
            @endif
            @endif
            <div class="form-check">
                @if ($showField)
                    {!! Form::checkbox($name, $options['value'], $options['checked'], $options['attr']) !!}

                    @if ($showLabel && $options['label'] !== false && $options['label_show'])
                        {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
                    @endif

                    @include('form.helpblock')
                @endif
                @include('form.errors')
            </div>

            @if($showLabel && $showField)
                @if($options['wrapper'] !== false)
        </div>
    @endif
@endif
