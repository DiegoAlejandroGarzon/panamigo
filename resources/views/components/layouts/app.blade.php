@extends('../themes/' . $activeTheme . '/' . $activeLayout)

@section('subcontent')
    {{ $slot }}
@endsection
