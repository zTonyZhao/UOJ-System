<?php
if (!Auth::check()) {
	redirectToLogin();
}
?>
<?php
$REQUIRE_LIB['dialog'] = '';
$REQUIRE_LIB['md5'] = '';
?>
<?php echoUOJPageHeader(UOJLocale::get('erase my account')) ?>
<h2 class="page-header" style="color:red"><?= UOJLocale::get('erase my account') ?></h2>
<p><?= UOJLocale::get('erase account warning') ?></p>
<p style="color:red"><?= UOJLocale::get('this operation can not be undone') ?></p>
<button type="button" class="btn btn-danger btn-sm"><?= UOJLocale::get('erase account') ?> <?=$myUser['username']?> </button>
<?php echoUOJPageFooter() ?>
