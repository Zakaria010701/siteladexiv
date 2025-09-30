<x-layouts.cms>
    <x-slot:title>{{$page->title}}</x-slot:title>
    <x-slot:description>{{$page->description}}</x-slot:description>
    <x-slot:keywords>{{$page->keywords}}</x-slot:keywords>
    <x-slot:colors>{{$colors}}</x-slot:colors>

    <x-cms.builder :components="$page->content"></x-cms.builder>
</x-layouts.cms>
