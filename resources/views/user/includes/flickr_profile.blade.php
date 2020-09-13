<div class="media">
    <div class="font-weight-bold align-self-start mr-3 pl-2 pt-2">Flickr Profile</div>
    <div class="media-body pt-2">
        <div>
            <label class="text-secondary">Name:</label> {{$activity->extra->title}}
        </div>
        <div>
            <label class="text-secondary">NSID:</label>  {{ $activity->extra->fields->Nsid }}
        </div>
        <div>
            <label class="text-secondary">Photos count:</label> {{ $activity->extra->fields->{'Photos count'} }}
        </div>
        <div>
            <label class="text-secondary">Original link:</label>
            <a href="{{$activity->extra->footer}}" target="_blank">{{$activity->extra->footer}}</a>
        </div>
    </div>
</div>

