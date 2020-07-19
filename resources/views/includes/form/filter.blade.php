<select
    class="form-control mr-sm-2 selectpicker"
    multiple
    data-live-search="true"
    name="{{$name}}[]"
    data-header="{{$title}}"
    title="{{$title}}" data-width="100%">
    @foreach ($options as $option)
        <option
            data-tokens="{{$option->getText()}}"
            @if($option->isSelected()) selected @endif
            value="{{$option->getValue()}}">{{$option->getText()}}</option>
    @endforeach
</select>
