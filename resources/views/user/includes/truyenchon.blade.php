<div class="media">
    <div class="font-weight-bold align-self-start mr-3 pl-2 pt-2">Truyện Chọn</div>
    <div class="media-body pt-2">
        <div>
            <label class="text-secondary">Title</label>
            <a href="{{route('truyenchon.story.view',['id' => $activity->object_id, 'chapter'=> 'chap-1'])}}">
                {{$activity->extra->title}}
            </a>
        </div>
    </div>
</div>

