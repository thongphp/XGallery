<div class="media">
    <div class="font-weight-bold align-self-start mr-3 pl-2 pt-2">JAV Movie</div>
    <div class="media-body pt-2">
        <div>
            <label class="text-secondary">Title</label>
            <a href="{{route('jav.movie.view',['id' => $activity->object_id])}}">
                {{$activity->extra->title}}
            </a>
        </div>
        <div>
            <label class="text-secondary">DVD ID</label> {{ $activity->extra->fields->{'DVD-ID'} }}
        </div>
        <div>
            <label class="text-secondary">Director</label> {{ $activity->extra->fields->Director }}
        </div>
        <div>
            <label class="text-secondary">Studio</label> {{ $activity->extra->fields->Studio }}
        </div>
    </div>
</div>
