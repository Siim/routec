<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php if($this->user->isLoggedIn()): ?>

<?php header('location: /') ?>

<?php else: ?>

<div class="login">
  <div class="loginbox">
    <form method="post" action="<?= PREFIX ?>/login">
      
      <div>
        <label for="username"><?= abc('Username') ?></label>
        <input type="text" name="username" id="username" />
      </div>
      <div>
        <label for="password"><?= abc('Password') ?></label>
        <input type="password" name="password" id="password" />
      </div>
      <button type="submit"><span>Login</span></button>
    </div>
  </form>
</div>
<?php endif ?>
</body>
</html>
