bbTopicJS = jQuery.extend( {
	currentUserId: '0',
	topicId: '0',
	favoritesLink: '',
	isFav: 0,
	confirmPostDelete: 'Are you sure you wanna delete this post?',
	favLinkYes: 'favorites',
	favLinkNo: '?',
	favYes: 'This topic is one of your %favLinkYes% [%favDel%]',
	favNo: '%favAdd% (%favLinkNo%)',
	favDel: 'x',
	favAdd: 'Add this topic to your favorites'
}, bbTopicJS );

bbTopicJS.isFav = parseInt( bbTopicJS.isFav );

jQuery( function($) {
	// Tags
	var tagsDelBefore = function( s ) {
		s.data['topic_id'] = bbTopicJS.topicId;
		return s;
	};
	$('#tags-list').wpList( { alt: '', delBefore: tagsDelBefore } );

	// Favorites
	var favoritesToggle = $('#favorite-toggle')
		.addClass( 'list:favorite' )
		.wpList( { alt: '', dimAfter: favLinkSetup } );

	var favoritesToggleSpan = favoritesToggle.children( 'span' )
		[bbTopicJS.isFav ? 'removeClass' : 'addClass' ]( 'is-not-favorite' );
	

	function favLinkSetup() {
		bbTopicJS.isFav = !favoritesToggleSpan.is('.is-not-favorite');
		var aLink = "<a href='" + bbTopicJS.favoritesLink + "'>";
		var aDim  = "<a href='" + favoritesToggleSpan.find( 'a[class^="dim:"]' ).attr( 'href' ) + "' class='dim:favorite-toggle:" + favoritesToggleSpan.attr( 'id' ) + ":is-not-favorite'>";
		if ( bbTopicJS.isFav ) {
			html = bbTopicJS.favYes
				.replace( /%favLinkYes%/, aLink + bbTopicJS.favLinkYes + "</a>" )
				.replace( /%favDel%/, aDim + bbTopicJS.favDel + "</a>" );
		} else {
			html = bbTopicJS.favNo
				.replace( /%favLinkNo%/, aLink + bbTopicJS.favLinkNo + "</a>" )
				.replace( /%favAdd%/, aDim + bbTopicJS.favAdd + "</a>" );
		}
		favoritesToggleSpan.html( html );
		favoritesToggle.wpList.process( favoritesToggleSpan );
	}

	// Posts
	var postConfirm = function(e,s,a) {
		if ( 'delete' != a ) {
			return true;
		}
		return confirm( bbTopicJS[ $('#' + s.element).is('.deleted') ? 'confirmPostUnDelete' : 'confirmPostDelete'] );
	};

	$('#thread').addClass( 'list:post' ).wpList( { alt: 'alt', altOffset: 1, confirm: postConfirm } );
} );
