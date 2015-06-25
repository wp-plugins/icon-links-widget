function ilw_initialize_sortable(){
	jQuery( '.ilw_sortable' ).sortable();
	jQuery( '.ilw_sortable' ).disableSelection();

	jQuery('.ilw_widget .widget-title-action, .ilw_widget .widget-title-action a').unbind();

	jQuery('.ilw_widget .widget-title-action').bind('click', function(){
		if( jQuery(this).parents('.ilw_widget').hasClass('open') ){
			jQuery(this).parents('.ilw_widget').removeClass('open');
			jQuery(this).parents('.ilw_widget').children('.widget-inside').slideUp(100);
		} else {
			jQuery(this).parents('.ilw_widget').addClass('open');
			jQuery(this).parents('.ilw_widget').children('.widget-inside').slideDown(100);
		}
	});

	jQuery('.ilw_widget .widget-title-action a').bind('click', function(e){
		e.preventDefault();
	});
}

jQuery(document).ajaxSuccess(function(e, xhr, settings){
	ilw_initialize_sortable();
});

jQuery(document).ready(function(){
	ilw_initialize_sortable();
});
