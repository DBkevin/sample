<!DOCTYPE html>
<html>
  <head>
  <!--  <title>Sample App</title>-->
  <title>@yield('title','Sample App') - Laravel 新手入门教程</title>
  <link rel="stylesheet" href="css/app.css">
  </head>
  <body>
      @include('layouts._header')
      <div class="container">
        @yield('content')
      @include('layouts._footer')
      </div>
  </body>
</html>