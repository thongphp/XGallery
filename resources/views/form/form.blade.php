@if($showStart)
    {{Form::open($formOptions)}}
@endif
@if($showFields)
    @foreach($fields as $field)
        @if (!in_array($field->getName(), $exclude))
            {!!$field->render()!!}
        @endif
    @endforeach
@endif
<div class="form-group">
    {!! Form::button('Save', ['type' => \Kris\LaravelFormBuilder\Field::BUTTON_SUBMIT, 'class' => 'btn btn-success']) !!}
</div>
@if($showEnd)
    {{Form::close()}}
@endif
