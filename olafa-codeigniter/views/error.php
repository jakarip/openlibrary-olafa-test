<?php
echo getLang("You are on this page because your account have some setting error. The error can occur because some of the following :")."<br><br>";
echo "1. &nbsp; &nbsp; ".getLang("Your account has not been included in any usergroup or")."<br><br>";
echo "2. &nbsp; &nbsp; ".getLang("Your account is already included in any usergroup but the menu has not been set in your usergroup or")."<br><br>";
echo "3. &nbsp; &nbsp; ".getLang("The menus has been set in your usergroup but the url has not been set in any menu of your usergroup")."<br><br>";

echo getLang("Please contact your administrator");
?>