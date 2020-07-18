@php
    use App\Repositories\ConfigRepository;
    $sortDirection = request()->request->get(ConfigRepository::KEY_SORT_DIRECTION, 'desc');
    $perPage = (int) request()->request->get(ConfigRepository::KEY_PER_PAGE, ConfigRepository::DEFAULT_PER_PAGE);
@endphp
<select class="custom-select form-control input-sm mr-sm-2"
        id="{{\App\Repositories\ConfigRepository::KEY_SORT_DIRECTION}}"
        name="{{\App\Repositories\ConfigRepository::KEY_SORT_DIRECTION}}"
>
    <option @if($sortDirection === 'asc') selected="selected" @endif value="asc">Asc</option>
    <option @if($sortDirection === 'desc') selected="selected" @endif value="desc">Desc</option>
</select>
<select class="custom-select form-control input-sm mr-sm-2"
        id="{{\App\Repositories\ConfigRepository::KEY_PER_PAGE}}"
        name="{{\App\Repositories\ConfigRepository::KEY_PER_PAGE}}"
>
    <option @if($perPage === 15) selected="selected" @endif value="15">15</option>
    <option @if($perPage === 30) selected="selected" @endif value="30">30</option>
    <option @if($perPage === 60) selected="selected" @endif value="60">60</option>
    <option @if($perPage === 120) selected="selected" @endif value="120">120</option>
</select>
