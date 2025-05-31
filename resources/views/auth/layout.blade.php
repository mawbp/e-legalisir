<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
  <style>
    body {
      background: linear-gradient(135deg, #0d6efd, #6ea8fe);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .login-card {
      background-color: white;
      color: black;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      padding: 30px;
      max-width: 400px;
      width: 100%;
      z-index: 2;
    }
    label.required:after { content: ' *'; color: red;}
  </style>
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <title>Sistem Legalisir Online</title>
</head>
<body>
  @yield('content')
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  @yield('script')
</body>
</html>