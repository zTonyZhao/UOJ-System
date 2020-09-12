<?php
	if (!Auth::check()) {
		redirectToLogin();
	}
	function handlePost() {
		global $myUser;
		if (!isset($_POST['old_password'])) {
			return '无效表单';
		}
		$old_password = $_POST['old_password'];
		if (!validatePassword($old_password) || !checkPassword($myUser, $old_password)) {
			return "失败：密码错误。";
		}
		if (isset($_POST['del_user'])) {
			// 
			DB::update("UPDATE submissions SET submitter='deleted_user' WHERE submitter='{$myUser['username']}'");
			DB::update("DELETE FROM blogs WHERE poster='{$myUser['username']}'");
			DB::update("DELETE FROM blogs_comments WHERE poster='{$myUser['username']}'");
			DB::update("UPDATE click_zans SET username='deleted_user' WHERE username='{$myUser['username']}'");
			DB::update("DELETE FROM problems_permissions WHERE username='{$myUser['username']}'");
			DB::update("UPDATE hacks SET hacker='deleted_user' WHERE hacker='{$myUser['username']}'");
			DB::update("UPDATE hacks SET owner='deleted_user' WHERE owner='{$myUser['username']}'");
			DB::update("DELETE FROM user_msg WHERE sender='{$myUser['username']}'");
			DB::update("DELETE FROM user_msg WHERE receiver='{$myUser['username']}'");
			DB::update("UPDATE custom_test_submissions SET submitter='deleted_user' WHERE submitter='{$myUser['username']}'");
			DB::update("DELETE FROM user_system_msg WHERE receiver='{$myUser['username']}'");
			DB::update("UPDATE best_ac_submissions SET submitter='deleted_user' WHERE submitter='{$myUser['username']}'");
			DB::update("DELETE FROM contests_registrants WHERE username='{$myUser['username']}'");
			DB::update("DELETE FROM contests_permissions WHERE username='{$myUser['username']}'");
			DB::update("UPDATE contests_asks SET username='deleted_user' WHERE username='{$myUser['username']}'");
			DB::update("UPDATE user_info SET usergroup='B',email='admin@example.com',password='f447b20a7fcbf53a5d5be013ea0b15af',rating=1500,qq=0,sex='U',ac_num=0,register_time='',remote_addr='127.0.0.1',http_x_forwarded_for='',motto='' WHERE username='{$myUser['username']}'");

			return "ok";
		}
		if ($_POST['ptag']) {
			$password = $_POST['password'];
			if (!validatePassword($password)) {
				return "失败：无效密码。";
			}
			$password = getPasswordToStore($password, $myUser['username']);
			DB::update("update user_info set password = '$password' where username = '{$myUser['username']}'");
		}

		$email = $_POST['email'];
		if (!validateEmail($email)) {
			return "失败：无效电子邮箱。";
		}
		$esc_email = DB::escape($email);
		DB::update("update user_info set email = '$esc_email' where username = '{$myUser['username']}'");

		if ($_POST['Qtag']) {
			$qq = $_POST['qq'];
			if (!validateQQ($qq)) {
				return "失败：无效QQ。";
			}
			$esc_qq = DB::escape($qq);
			DB::update("update user_info set qq = '$esc_qq' where username = '{$myUser['username']}'");
		} else {
			DB::update("update user_info set QQ = NULL where username = '{$myUser['username']}'");
		}
		if ($_POST['sex'] == "U" || $_POST['sex'] == 'M' || $_POST['sex'] == 'F') {
			$sex = $_POST['sex'];
			$esc_sex = DB::escape($sex);
			DB::update("update user_info set sex = '$esc_sex' where username = '{$myUser['username']}'");
		}
		
		if (validateMotto($_POST['motto'])) {
			$esc_motto = DB::escape($_POST['motto']);
			DB::update("update user_info set motto = '$esc_motto' where username = '{$myUser['username']}'");
		}
		
		return "ok";
	}
	if (isset($_POST['change'])) {
		die(handlePost());
	}
?>
<?php
	$REQUIRE_LIB['dialog'] = '';
	$REQUIRE_LIB['md5'] = '';
?>
<?php echoUOJPageHeader(UOJLocale::get('modify my profile')) ?>
<h2 class="page-header"><?= UOJLocale::get('modify my profile') ?></h2>
<form id="form-update" class="form-horizontal">
	<h4><?= UOJLocale::get('please enter your password for authorization') ?></h4>
	<div id="div-old_password" class="form-group">
		<label for="input-old_password" class="col-sm-2 control-label"><?= UOJLocale::get('password') ?></label>
		<div class="col-sm-3">
			<input type="password" class="form-control" name="old_password" id="input-old_password" placeholder="<?= UOJLocale::get('enter your password') ?>" maxlength="20" />
			<span class="help-block" id="help-old_password"></span>
		</div>
	</div>
	<h4><?= UOJLocale::get('please enter your new profile') ?></h4>
	<div id="div-password" class="form-group">
		<label for="input-password" class="col-sm-2 control-label"><?= UOJLocale::get('new password') ?></label>
		<div class="col-sm-3">
			<input type="password" class="form-control" id="input-password" name="password" placeholder="<?= UOJLocale::get('enter your new password') ?>" maxlength="20" />
			<input type="password" class="form-control top-buffer-sm" id="input-confirm_password" placeholder="<?= UOJLocale::get('re-enter your new password') ?>" maxlength="20" />
			<span class="help-block" id="help-password"><?= UOJLocale::get('leave it blank if you do not want to change the password') ?></span>
		</div>
	</div>
	<div id="div-email" class="form-group">
		<label for="input-email" class="col-sm-2 control-label"><?= UOJLocale::get('email') ?></label>
		<div class="col-sm-3">
			<input type="email" class="form-control" name="email" id="input-email" value="<?=$myUser['email']?>" placeholder="<?= UOJLocale::get('enter your email') ?>" maxlength="50" />
			<span class="help-block" id="help-email"></span>
		</div>
	</div>
	<div id="div-qq" class="form-group">
		<label for="input-qq" class="col-sm-2 control-label"><?= UOJLocale::get('QQ') ?></label>
		<div class="col-sm-3">
			<input type="text" class="form-control" name="qq" id="input-qq" value="<?= $myUser['qq'] != 0 ? $myUser['qq'] : '' ?>" placeholder="<?= UOJLocale::get('enter your QQ') ?>" maxlength="50" />
			<span class="help-block" id="help-qq"></span>
		</div>
	</div>
	<div id="div-sex" class="form-group">
		<label for="input-sex" class="col-sm-2 control-label"><?= UOJLocale::get('sex') ?></label>
		<div class="col-sm-3">
			<select class="form-control" id="input-sex"  name="sex">
				<option value="U"<?= Auth::user()['sex'] == 'U' ? ' selected="selected"' : ''?>><?= UOJLocale::get('refuse to answer') ?></option>
				<option value="M"<?= Auth::user()['sex'] == 'M' ? ' selected="selected"' : ''?>><?= UOJLocale::get('male') ?></option>
				<option value="F"<?= Auth::user()['sex'] == 'F' ? ' selected="selected"' : ''?>><?= UOJLocale::get('female') ?></option>
			</select>
		</div>
	</div>
	<div id="div-motto" class="form-group">
		<label for="input-motto" class="col-sm-2 control-label"><?= UOJLocale::get('motto') ?></label>
		<div class="col-sm-3">
			<textarea class="form-control" id="input-motto"  name="motto"><?=HTML::escape($myUser['motto'])?></textarea>
			<span class="help-block" id="help-motto"></span>
		</div>
	</div>
	<div class="form-group">
    	<div class="col-sm-offset-2 col-sm-3">
	      <p class="form-control-static"><strong><?= UOJLocale::get('change avatar help') ?></strong></p>
	    </div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-3">
			<button type="submit" id="button-submit" class="btn btn-secondary"><?= UOJLocale::get('submit') ?></button>
		</div>
	</div>
</form>
<div class="alert alert-danger" role="alert">
  <?= UOJLocale::get('danger zone') ?>
</div>
<form id="user-delete" class="form-horizontal">
	<h4><?= UOJLocale::get('please enter your password for authorization') ?></h4>
	<div id="div-old_password_deluser" class="form-group">
		<label for="input-old_password_deluser" class="col-sm-2 control-label"><?= UOJLocale::get('password') ?></label>
		<div class="col-sm-3">
			<input type="password" class="form-control" name="old_password_deluser" id="input-old_password_deluser" placeholder="<?= UOJLocale::get('enter your password') ?>" maxlength="20" />
			<span class="help-block" id="help-old_password_deluser"></span>
		</div>
	</div>
	<h4><?= UOJLocale::get('delete user') ?></h4>
	<div class="form-group">
        <div class="col-sm-offset-2 col-sm-3">
	<p><?= UOJLocale::get('delete user warning') ?></p>
	<p style="color:red"><?= UOJLocale::get('this operation can not be undone') ?></p>
	<button type="submit" id="button-submit-2" class="btn btn-danger"><?= UOJLocale::get('delete user') ?> <?=$myUser['username']?> </button>
            </div>
        </div>
</form>

<script type="text/javascript">
	function validateUpdatePost() {
		var ok = true;
		ok &= getFormErrorAndShowHelp('email', validateEmail);
		ok &= getFormErrorAndShowHelp('old_password', validatePassword);

		if ($('#input-password').val().length > 0)
			ok &= getFormErrorAndShowHelp('password', validateSettingPassword);
		if ($('#input-qq').val().length > 0)
			ok &= getFormErrorAndShowHelp('qq', validateQQ);
		ok &= getFormErrorAndShowHelp('motto', validateMotto);
		return ok;
	}
	function submitUpdatePost() {
		if (!validateUpdatePost())
			return;
		$.post('/user/modify-profile', {
			change   : '',
			etag     : $('#input-email').val().length,
			ptag     : $('#input-password').val().length,
			Qtag     : $('#input-qq').val().length,
			email    : $('#input-email').val(),
			password : md5($('#input-password').val(), "<?= getPasswordClientSalt() ?>"),
			old_password : md5($('#input-old_password').val(), "<?= getPasswordClientSalt() ?>"),
			qq       : $('#input-qq').val(),
			sex      : $('#input-sex').val(),
			motto    : $('#input-motto').val()
		}, function(msg) {
			if (msg == 'ok') {
				BootstrapDialog.show({
					title   : '修改成功',
					message : '用户信息修改成功',
					type    : BootstrapDialog.TYPE_SUCCESS,
					buttons : [{
						label: '好的',
						action: function(dialog) {
							dialog.close();
						}
					}],
					onhidden : function(dialog) {
						window.location.href = '/user/profile/<?=$myUser['username']?>';
					}
				});
			} else {
				BootstrapDialog.show({
					title   : '修改失败',
					message : msg,
					type    : BootstrapDialog.TYPE_DANGER,
					buttons: [{
						label: '好的',
						action: function(dialog) {
							dialog.close();
						}
					}],
				});
			}
		});
	}
	function UserDeletePost() {
		if (!getFormErrorAndShowHelp('old_password_deluser', validatePassword))
			return;
		$.post('/user/modify-profile', {
			change       : '',
			del_user     : '',
			old_password : md5($('#input-old_password_deluser').val(), "<?= getPasswordClientSalt() ?>")
		}, function(msg) {
			console.log(msg);
			if (msg == 'ok') {
				BootstrapDialog.show({
					title   : '删除成功',
					message : '用户删除成功',
					type    : BootstrapDialog.TYPE_SUCCESS,
					buttons : [{
						label: '好的',
						action: function(dialog) {
							dialog.close();
						}
					}],
					onhidden : function(dialog) {
						window.location.href = '<?= HTML::url('/logout?_token='.crsf_token()) ?>';
					}
				})
			}
			else 
			{
				BootstrapDialog.show({
					title   : '删除失败',
					message : msg,
					type    : BootstrapDialog.TYPE_DANGER,
					buttons  : [{
						label: '好的',
						action: function(dialog) {
							dialog.close();
						}
					}]
				})
			}
		})
	}
	function deleteuser2step() {
		BootstrapDialog.show({
			title   : '确认删除',
			message : '您确认删除您的账号吗？此过程不可逆。',
			type    : BootstrapDialog.TYPE_DANGER,
			buttons : [{
				label: '确定',
				action: function(dialog) {
					UserDeletePost();
					dialog.close();
				}
			},{
				label: '取消',
				action: function(dialog) {
					dialog.close();
				}
			}]
		})
	}
	$(document).ready(function(){$('#form-update').submit(function(e) {submitUpdatePost();e.preventDefault();});
	});
	$(document).ready(function(){
		$('#user-delete').submit(
			function(e) {
				deleteuser2step();
				e.preventDefault()
			}
		)
		
	});
</script>
<?php echoUOJPageFooter() ?>

