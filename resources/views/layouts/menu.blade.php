@if (auth()->user()->role == 'Admin')
    @include('layouts.menu.admin')
@elseif(auth()->user()->role == 'User')
    @include('layouts.menu.user')
@endif
