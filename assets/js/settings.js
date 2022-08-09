jQuery( document ).ready(
	function ( $ ) {
		$( '#add-more-custom' ).cloneData( {
			mainContainerId: 'main-container',
			cloneContainer: 'container-item',
			removeButtonClass: 'remove-item',
			removeConfirm: true, 
			removeConfirmMessage: vsl_settings_vars.removeConfirmMessage,
			excludeHTML: ".exclude"
		} );

		$( 'body .custom-fields-container' ).on( 'change', '.select-custom-type', function( event ) {
			var $target = $( event.currentTarget );
        	var selected = $target.val();
			switch( selected ) {
				case 'select':
					$( '<div>', {
						class: "col-12 mb-space exclude",
						html: [
							$( '<label>', {
								class: 'form-label',
								html: vsl_settings_vars.labelMessage
							} ), 
							$( '<textarea>', {
								name: $( event.target ).closest( ".container-item" ).attr( "data-name" ) + '[' + $( event.target ).closest( ".container-item" ).attr( "data-index" ) + '][options]',
								class: 'form-control',
								required: 'required'
							} ), 
							$( '<small>', {
								html: vsl_settings_vars.helpMessage,
								class: 'text-muted'
							} )
						]
					} ).insertAfter( $( event.target ).closest( ".col-4" ) );
					break;
				default:
					$( event.target ).closest( ".row" ).find( ".exclude" ).slideUp( function() {
						$( this ).remove();
					} );
					break;
			} 
		} );
	}
);
