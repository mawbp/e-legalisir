<html>
    <head></head>
    <body>
        <form action="{{ route('api.midtrans.not') }}" method="POST">
            @csrf
            <input type="text" name="ronde" >
            <button type="submit">Kirim</button>
        </form>
    </body>
</html>