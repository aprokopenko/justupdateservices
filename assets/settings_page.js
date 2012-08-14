jQuery(document).ready(function(){
	jusmDelSiteFromList();
	jusmDelSitesList();
	jusmPingSite();
});

function jusmDelSiteFromList() {
	var editBox = jQuery('#jusm-edit-box');
	var classEven = 'odd';
	
	jQuery('ul.jusm-ping-sites-list a.jusm-delete-variable').click(function() {
		var _this = jQuery(this);
		var jusmHolder = _this.parents('li').eq(0);
		var jusmSite = jQuery('.jusm-site', jusmHolder).text();
		
		if( confirm( text_jusm.confirm_delete_single.replace('!ping_site', jusmSite) ) ) {
			var data = {
				action: 'jusm_ajax_remove_url',
				jusm_del_ping_sites: jusmSite
			};
			
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				if( jQuery('li', editBox).length ) {
					jusmHolder.remove();
					jQuery('li', editBox).removeClass(classEven);
					jQuery('li:odd', editBox).addClass(classEven);
				}
				else {
					editBox.remove();
				}
			})
			.error(function() {
				alert( text_jusm.err_ajax_delete.replace('!ping_site', jusmSite) );
			});
		}
		
		return false;
	});
}

function jusmDelSitesList() {
	jQuery('#jusm-del-form').submit(function(){
		if( jQuery('#jusm-del-ping-sites').val().length ) {
			var str = jQuery('#jusm-del-ping-sites').val();
			if( str.length > 100 ) {
				str = str.substr(0, 80) + ' ...';
			}
			
			if( !confirm( text_jusm.confirm_delete_multiple ) ) {
				return false;
			}
		}
		else {
			alert( text_jusm.err_validate_delete_urls );
			return false;
		}
	});
}

function jusmPingSite() {
	var flag = false;
	var btns = jQuery('a.jusm-btn-ping');
	var classPingOn = 'jusm-btn-ping-on';
	var classSiteLoader = 'jusm-site-loader';
	var classSiteGood = 'jusm-site-good';
	var classSiteBad = 'jusm-site-bad';
	var list = jQuery('ul.jusm-ping-sites-list > li');
	var ajax;
	var index;
	
	btns.click(function(){
		var _this = jQuery(this);
		
		if(flag) {
			flag = false;
			ajax.abort();
			jQuery('.' + classSiteLoader, list).removeClass(classSiteLoader);
			btns.removeClass(classPingOn);
		}
		else {
			flag = true;
			index = 0;
			jusmCheckPing();
			jQuery('.jusm-site-info', list).removeClass(classSiteLoader).removeClass(classSiteGood).removeClass(classSiteBad);
			btns.addClass(classPingOn);
		}
		
		return false;
	});
	
	function jusmCheckPing() {
		if( index < list.length && flag ) {
			var jusmSite = jQuery('.jusm-site', list.eq(index)).text();
			var jusmSiteInfo = jQuery('span.jusm-site-info', list.eq(index));
			
			jusmSiteInfo.addClass(classSiteLoader);
			
			var data = {
				action: 'jusm_ajax_ping_site',
				jusm_ping_site: jusmSite
			};
			
			ajax = jQuery.post(ajaxurl, data, function(response) {
				jusmSiteInfo.removeClass(classSiteLoader);
				if ( response == 200 || response == 301 || response == 302 ) {
					jusmSiteInfo.addClass(classSiteGood);
				}
				else {
					jusmSiteInfo.addClass(classSiteBad);
				}
				index++;
				jusmCheckPing();
			})
			.error(function() {
				if( flag ) {
					flag = false;
					btns.removeClass(classPingOn);
					alert( text_jusm.err_ajax_ping );
				}
			});
		}
		else {
			flag = false;
			btns.removeClass(classPingOn);
		}
	}
}
