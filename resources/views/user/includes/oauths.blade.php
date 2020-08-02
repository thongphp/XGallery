<div class="media">
    <div class="font-weight-bold align-self-start mr-3 pl-2 pt-2">OAuth</div>
    <div class="media-body pt-2">
        <div>
            <label class="text-secondary">ID:</label> {{$activity->object_id}}
        </div>
        <div>
            <label class="text-secondary">Account Name:</label> {{$activity->extra->title}}
        </div>
        <div>
            <label class="text-secondary">Email:</label> {{$activity->extra->fields->Email}}
        </div>
    </div>
</div>
