<div>
    @if(Auth::check())
        {{-- お気に入りボタン --}}
        @if(!\Auth::user()->is_favorites($micropost->id))
            {!! Form::open(['route' => ['favorites.favorite',$micropost->id]]) !!}
                {!! Form::submit('Favorite',['class' => 'btn btn-success btn-sm']) !!}
            {!! Form::close() !!}
        @else
            {!! Form::open(['route' => ['favorites.unfavorite',$micropost->id], 'method' => 'delete']) !!}
                {!! Form::submit('unFavorite',['class' => 'btn btn-warning btn-sm']) !!}
            {!! Form::close() !!}
        @endif
    @endif
</div>
