@if($position === 'center')
<div @class([
    "cms-title-block",
    "py-40",
    "bg-center",
    "bg-no-repeat" => !is_null($image),
    "bg-cover" => !is_null($image),
]) style="background-image: url({{$image}})">
    <h1 class="m-auto text-center">{{ $content['title'] }}</h1>
</div>
@else
<div class="cms-title-block py-40 flex items-center {{ $position === 'left' ? 'flex-row' : 'flex-row-reverse' }}">
    @if(!is_null($image))
    <div class="w-1/2">
        <img src="{{ $image }}" alt="" class="w-full h-auto">
    </div>
    @endif
    <div class="w-1/2 {{ $position === 'left' ? 'pl-4' : 'pr-4' }}">
        <h1>{{ $content['title'] }}</h1>
    </div>
</div>
@endif
