<div>
    @foreach($components as $key => $value)
        @php
            $component = \App\Enums\Cms\CmsBuilderBlock::tryFrom($value['type']);
        @endphp
        @isset($component)
        <x-dynamic-component :component="$component->getComponentName()" :content="$value['data']"></x-dynamic-component>
        @endisset
    @endforeach
</div>
