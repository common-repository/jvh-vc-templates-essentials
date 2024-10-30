jQuery(function($) {
	jQuery('.vc_templates-button').on('click', function() {
		placeJvhTemplatesOnTop();
	});

	jQuery.fn.reverse = [].reverse;

	function placeJvhTemplatesOnTop() {
		var jvhTemplates = $('.jvh_template');
		var templatesWrapper = jvhTemplates.first().parent();

		jvhTemplates.reverse().each(function() {
			$(this).prependTo(templatesWrapper);
		});
	}
});
