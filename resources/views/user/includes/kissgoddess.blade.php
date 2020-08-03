<div class="media">
    <div class="font-weight-bold align-self-start mr-3 pl-2 pt-2">KissGoddess</div>
    <div class="media-body pt-2">
        <div>
            <label class="text-secondary">Title</label>
            <a href="{{route('kissgoddess.item.view',['id' => $activity->extra->fields->ID])}}">
                {{$activity->extra->title}}
            </a>
        </div>
        <div>
            <label class="text-secondary">Photos count</label> {{ $activity->extra->fields->{'Photos count'} }}
        </div>
        <div>
            <label class="text-secondary">Original link</label>
            <a href="{{$activity->extra->footer}}" target="_blank">{{$activity->extra->footer}}</a>
        </div>
    </div>
</div>
