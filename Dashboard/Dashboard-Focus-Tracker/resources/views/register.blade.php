<h1>Register</h1>

<form method="POST" action="/register">
    @csrf

    <label>Username</label><br>
    <input type="text" name="username"><br><br>

    <label>Email</label><br>
    <input type="email" name="email"><br><br>

    <label>Password</label><br>
    <input type="password" name="password"><br><br>

    <button type="submit">Create account</button>
</form>

<p>Already have an account?</p>
<a href="/login">Login here</a>