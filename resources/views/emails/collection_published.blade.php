<p>Hi {{ $user->name }},</p>

<p>A new resource collection is now available:</p>

<h3>{{ $collection->title }}</h3>

@if($collection->description)
    <p>{{ $collection->description }}</p>
@endif


<p>Published by Admin.</p>
