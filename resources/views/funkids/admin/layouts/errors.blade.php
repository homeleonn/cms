@foreach ($errors->all() as $error)
	<div style="background: #ffa6a6; padding: 10px 5px;margin: 5px;border-radius: 5px;">{{ $error }}</div>
@endforeach

@if (session()->has('flash_errors'))
	@foreach(session()->get('flash_errors') as $error)
		<div style="background: #ffa6a6; padding: 10px 5px;margin: 5px;border-radius: 5px;">{!! $error !!}</div>
	@endforeach
@endif

@if (session()->has('messages'))
	@foreach(session()->get('messages') as $message)
		<div style="background: lightblue; padding: 10px 5px;margin: 5px;border-radius: 5px;">{!! $message !!}</div>
	@endforeach
@endif