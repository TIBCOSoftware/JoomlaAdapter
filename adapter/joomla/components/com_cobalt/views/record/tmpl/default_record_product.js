/*
 * @copyright Copyright © 2013, TIBCO Software Inc. All rights reserved.
 * @license GNU General Public License version 2; see LICENSE.txt
 */

jQuery(function(){
	jQuery("button.openapi-send-request").live("click",function(){
		var data = {};
		data.options = 'com_cobalt';
		data.task = 'ajaxmore.requestPlan';
		data.plan_id = jQuery(this).data('planid');
		data.product_id = jQuery(this).data('prodid');
		console.log(data);
		jQuery.ajax({
					  url:'',
					  data:data
					}).done(function(){
						
					});	
	});
});
