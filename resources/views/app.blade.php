@extends('common::framework')

@section('angular-styles')
    {{--angular styles begin--}}
		<link rel="stylesheet" href="client/styles.3dd3bf15c5d7d80c5229.css" media="print" onload="this.media=&apos;all&apos;">
		<link rel="stylesheet" href="client/styles.3dd3bf15c5d7d80c5229.css">
	{{--angular styles end--}}
@endsection

@section('angular-scripts')
    {{--angular scripts begin--}}
		<script>
        setTimeout(function() {
            var spinner = document.querySelector('.global-spinner');
            if (spinner) spinner.style.display = 'flex';
        }, 50);
    </script>
		<script src="client/runtime.ef2b0ca9fa5a2aedb3f4.js" defer></script>
		<script src="client/polyfills.e85190139dc8f6acda60.js" defer></script>
		<script src="client/main.4d24f09914645054fd32.js" defer></script>
	{{--angular scripts end--}}
@endsection
