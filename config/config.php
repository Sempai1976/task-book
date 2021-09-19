<?php
define("BASEPATH", $_SERVER['DOCUMENT_ROOT']);
define("BASE_URL", get_base_url());
define("ADMIN_GROUP", 1); //group of admin
define("MEMBER_GROUP", 3); //group of member
define("AUTOLOGIN_COOKIE_NAME", 'autologin');
define("AUTOLOGIN_COOKIE_LIFE", (86400 * 30)); // 86400 = 1 day
define("ENABLE_REGISTRATION", true);
define("CAN_GUESTS_CREATE_TASKS", true);
define("CAN_GUESTS_EDIT_TASKS", true); //if true use cookies
define("ITEMS_ON_PAGE", 5); //for pagination
?>
