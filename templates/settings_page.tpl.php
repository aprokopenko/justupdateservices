<?php
	$assets_path = WP_PLUGIN_URL.'/just-update-services-merge/assets';
?>
<div class="wrap">
	<div class="icon32 icon32-posts-page" id="icon-edit"><br></div>
	<h2>Just Update Services</h2>
	
	<h3><?php _e('Update Services') ?></h3>
		
		<?php if ( 1 == get_option('blog_public') ) : ?>
			
			<p><?php _e('When you publish a new post, WordPress automatically notifies the following site update services. For more about this, see <a href="http://codex.wordpress.org/Update_Services">Update Services</a> on the Codex. Separate multiple service <abbr title="Universal Resource Locator">URL</abbr>s with line breaks.') ?></p>	
			
			<div class="jusm-holder">
				<?php // Form add ?>
				<form id="jusm-add-form" class="alignleft width-49" action="?page=jusm_update_services" method="post">
					
					<h3>Add</h3>
					<p><label for="jusm-add-ping-sites">To add more services to the list use the textarea below (don't worry if you paste duplicated URL)</label></p>
					
					<textarea id="jusm-add-ping-sites" name="jusm_add_ping_sites" class="large-text code" rows="3"></textarea>
					
					<p class="submit">
						<input id="jusm-add-submit" class="button-primary alignright" type="submit" value="Save Changes" name="jusm_submit">
					</p>
				</form>
				
				<?php // Form remove ?>
				<form id="jusm-del-form" class="alignright width-49" action="?page=jusm_update_services" method="post">
					
					<h3>Delete</h3>
					<p><label for="jusm-del-ping-sites">Delete services list</label></p>
					
					<textarea id="jusm-del-ping-sites" name="jusm_del_ping_sites" class="large-text code" rows="3"></textarea>
					
					<p class="submit">
						<input id="jusm-del-submit" class="button-primary alignright" type="submit" value="Save Changes" name="jusm_submit">
					</p>
					
				</form>
				
			</div>
			
			<?php if( !empty($jusm_ping_sites) ) : ?>
				
				<div id="jusm-edit-box" class="jus-holder">
					<div class="jusm-holder">
						<h3 class="alignleft">Edit current list</h3>
						<a class="alignright button-secondary jusm-btn-ping" href="#">
							<span class="jusm-text-ping">Ping</span>
							<span class="jusm-text-stop">Stop</span>
						</a>
					</div>
					<p class="jusm-note">You have <?php echo count($jusm_ping_sites); ?> site(s) in Update Services list.</p>
					<ul class="jusm-ping-sites-list">
					<?php
						$odd = 0;
						
						foreach( $jusm_ping_sites as $item ) {
							// add even class
							if ($odd % 2) echo '<li class ="odd">';
							else echo '<li>';
							
							echo 	'<span class="jusm-site">'.$item.'</span>
									<span class="jusm-row-options">
										<span class="jusm-site-info"></span>
										<a href="#" class="jusm-delete-variable" title="Delelte">
											<img src="'.$assets_path.'/icon-delete.png" title="Delete" alt="Delete" />
										</a>
									</span>
								</li>';
							
							$odd++;
						}
					?>
					</ul>
					<div class="jusm-holder">
						<span class="jusm-note alignleft">* URLs are checking on 200, 301 and 302 the response codes</span>
						<a class="alignright button-secondary jusm-btn-ping" href="#">
							<span class="jusm-text-ping">Ping</span>
							<span class="jusm-text-stop">Stop</span>
						</a>
					</div>
				</div>
				
			<?php endif; ?>
			
		<?php else : ?>
		
			<p><?php printf(__('WordPress is not notifying any <a href="http://codex.wordpress.org/Update_Services">Update Services</a> because of your site&#8217;s <a href="%s">privacy settings</a>.'), 'options-privacy.php'); ?></p>
		
		<?php endif; ?>
		
</div>
