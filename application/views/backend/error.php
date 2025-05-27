
 <div class="row">
	<div class="col-lg-12 portlets">
	  <div class="panel">
		<div class="panel-header bg-primary">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
		</div>
		<div class="panel-content">
			<div class="filter-left">
				<?php
				echo getLang("you_are_on_this_page_because_your_account_have_some_setting_error._the_error_can_occur_because_some_of_the_following_:")."<br><br>";
				echo "1. &nbsp; &nbsp; ".getLang("your_account_has_not_been_included_in_any_usergroup,_or")."<br><br>";
				echo "2. &nbsp; &nbsp; ".getLang("your_account_is_already_included_in_any_usergroup_but_the_menu_has_not_been_set_in_your_usergroup,_or")."<br><br>";
				echo "3. &nbsp; &nbsp; ".getLang("the_menus_has_been_set_in_your_usergroup_but_the_url_has_not_been_set_in_any_menu_of_your_usergroup")."<br><br>";

				echo getLang("please_contact_your_administrator");
				?>
			</div>
		</div>
	  </div>
	</div>
</div>
<?php $this->load->view('backend/theme_footer'); ?>