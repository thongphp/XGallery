<select class="form-control mr-sm-2 selectpicker" multiple data-live-search="true" name="filter_{{$name}}[]">
    @foreach ($options as $field)
        <option data-tokens="{{$field}}" value="{{$field}}">{{$field}}</option>
    @endforeach
</select>
