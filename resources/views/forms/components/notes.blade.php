<div {{ $attributes }}>
    @foreach ($getRecord()->notes as $note)
        {{$note->content}}
    @endforeach
</div>
