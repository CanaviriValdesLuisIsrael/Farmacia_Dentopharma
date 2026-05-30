@if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all as $error)
            <li>{{$rror}}</li>
            <h1>holla</h1>
        @endforeach
    </ul>
</div>
    
@endif