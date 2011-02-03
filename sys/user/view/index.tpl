<html>
<head>
  <title>hello index</title>
</head>
<body>

<?php if($user->isLoggedIn()): ?>
<strong>yay. you are in!</strong>
<a href="/logout">logout</a>
<?php else: ?>
<form method="post" action="/login">
  <label for="username">Username</label>
  <input type="text" name="username" />
  <label for="password">Password</label>
  <input type="password" name="password" />
  <input type="submit" />

</form>
<?php endif ?>
</body>
</html>
