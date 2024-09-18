<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <meta http-equiv="cleartype" content="on">
        @vite('resources/js/app.css')
	</head>
	<body>
		<div style="width:{{ $viewportSize ?? '1000px' }}; margin:{{ $viewportMargin ?? '0 auto' }};">
			@yield('body')
		</div>
	</body>
</html>
