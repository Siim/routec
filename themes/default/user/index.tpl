<html>
<head>
  <title>hello index</title>
</head>
<body>

<?php if($this->user->isLoggedIn()): ?>
<strong><?= abc('yay') ?>. you are in!</strong>
<a href="/logout"><?= abc('logout') ?></a> | 
<a href="/language/estonian">Et</a> |
<a href="/language/english">En</a> 

<?php else: ?>
<form method="post" action="/login">
  <label for="username"><?= abc('Username') ?></label>
  <input type="text" name="username" />
  <label for="password"><?= abc('Password') ?></label>
  <input type="password" name="password" />
  <input type="submit" />

</form>
<?php endif ?>
</body>
</html>
