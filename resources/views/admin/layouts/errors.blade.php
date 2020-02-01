@foreach ($errors->all() as $error)
	<div style="background: red; padding: 10px 5px;">{{ $error }}</div>
@endforeach