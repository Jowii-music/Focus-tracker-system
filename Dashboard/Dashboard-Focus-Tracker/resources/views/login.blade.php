<h1>Login</h1>

@if (session('error'))
    <p style="color: red;">{{ session('error') }}</p>
@endif

@if (session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<form method="POST" action="/login">
    @csrf

    <label>Email</label><br>
    <input type="email" name="email"><br><br>

    <label>Password</label><br>
    <input type="password" name="password"><br><br>

    <button type="submit">Login</button>
</form>

<p>No account?</p>
<a href="/register">Register here</a>