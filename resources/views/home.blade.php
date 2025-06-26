@extends('layouts.app')

@section('content')
   <div class="main">
    <h1>lorem ipsum</h1>

    <div class="content-grid">
        <div class="left-column">
            <div class="card box-1"></div>
            <div class="card box-2"></div>
        </div>

        <div class="card large box-complete">Complete Program</div>
        <div class="card large box-modular">Modular Enrollment</div> {{-- pushed to the very right --}}
    </div>
</div>

@endsection
