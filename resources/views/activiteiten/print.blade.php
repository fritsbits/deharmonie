@extends('layouts.print')
@section('title', $activiteit->titel_nl)
@section('content')
<p>Print: {{ $activiteit->titel_nl }}</p>
@endsection
