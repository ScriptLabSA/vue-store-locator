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
					$( event.target ).closest( ".row" ).find( ".exclude" ).slideUp( function() {
						$( this ).remove();
					} );

					$( '<div>', {
						class: "col-12 mb-space exclude",
						html: [
							$( '<label>', {
								class: 'form-label',
								for: $( event.target ).closest( ".container-item" ).attr( "data-name" ) + '_options_' + $( event.target ).closest( ".container-item" ).attr( "data-index" ),
								html: vsl_settings_vars.labelMessage
							} ), 
							$( '<textarea>', {
								id: $( event.target ).closest( ".container-item" ).attr( "data-name" ) + '_options_' + $( event.target ).closest( ".container-item" ).attr( "data-index" ),
								name: $( event.target ).closest( ".container-item" ).attr( "data-name" ) + '[' + $( event.target ).closest( ".container-item" ).attr( "data-index" ) + '][options]',
								class: 'form-control',
								required: 'required'
							} ), 
							$( '<small>', {
								html: vsl_settings_vars.helpMessage,
								class: 'text-muted'
							} )
						]
					} ).add(
						$( '<div>', {
							class: "col-12 mb-space form-check exclude",
							html: [
								$( '<input>', {
									type: 'checkbox',
									value: '1',
									id: $( event.target ).closest( ".container-item" ).attr( "data-name" ) + '_multiple_' + $( event.target ).closest( ".container-item" ).attr( "data-index" ),
									name: $( event.target ).closest( ".container-item" ).attr( "data-name" ) + '[' + $( event.target ).closest( ".container-item" ).attr( "data-index" ) + '][multiple]',
									class: 'form-check-input',
									required: 'required'
								} ),
								$( '<label>', {
									for: $( event.target ).closest( ".container-item" ).attr( "data-name" ) + '_multiple_' + $( event.target ).closest( ".container-item" ).attr( "data-index" ),
									class: 'form-check-label',
									html: vsl_settings_vars.labelMultiple
								} )
							]
						} )
					).insertAfter( $( event.target ).closest( ".col-4" ) );
					break;
				case 'country':
					$( event.target ).closest( ".row" ).find( ".exclude" ).slideUp( function() {
						$( this ).remove();
					} );
					$( '<div>', {
						class: "col-12 mb-space form-check exclude",
						html: [
							$( '<input>', {
								type: 'checkbox',
								value: '1',
								id: $( event.target ).closest( ".container-item" ).attr( "data-name" ) + '_multiple_' + $( event.target ).closest( ".container-item" ).attr( "data-index" ),
								name: $( event.target ).closest( ".container-item" ).attr( "data-name" ) + '[' + $( event.target ).closest( ".container-item" ).attr( "data-index" ) + '][multiple]',
								class: 'form-check-input'
							} ),
							$( '<label>', {
								for: $( event.target ).closest( ".container-item" ).attr( "data-name" ) + '_multiple_' + $( event.target ).closest( ".container-item" ).attr( "data-index" ),
								class: 'form-check-label',
								html: vsl_settings_vars.labelMultiple
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

		var adjustment;
		$( ".custom-fields-container" ).sortable( {
			containerSelector: '.custom-fields-container',
			itemSelector: '.container-item',
			placeholder: '<div class="card container-item placeholder"></div>',
			draggedClass: 'field-dragged',
			handle: 'i.grid-card-move',
			onDrop: function( $item, container, _super ) {
				updateMetaIndex();
				_super( $item, container );
			},
            serialize: function ( parent, children, isContainer ) {
                return isContainer ? [children] : parent.data( 'index' );
            }
		});
		  
		updateMetaIndex = function () {
			var indexValue = 0;
			$( '.custom-fields-container .container-item' ).each( function() {
				var $containerItem = $( this );
				$containerItem.find( 'input, select, textarea' ).each( function() {
					var $elem = $( this );

					var name = $elem.attr( 'name' );
					if ( name !== undefined ) {
						var matches = name.match( /(^.+?)([\[\d{1,}\]]{1,})(\[.+\]$)/i );
		
						if ( matches && matches.length === 4 ) {
							matches[2] = matches[2].replace( /\]\[/g, "-" ).replace (/\]|\[/g, '' );
							var identifiers = matches[2].split( '-' );
							identifiers[0] = indexValue;
		
							name = matches[1] + '[' + identifiers.join('][') + ']' + matches[3];
							$elem.attr( 'name', name );
						}
					}

					var id = $elem.attr( 'id' );
					var newID = id;

					if ( id !== undefined ) {
						newID = incrementMetaIndex( id, indexValue );
						$elem.attr( 'id', newID );
						$elem.parent().find( 'label' ).attr( 'for', newID );
					}
				} );

				$containerItem.attr( 'data-index', indexValue );

				indexValue++;
            });
		}

		var incrementMetaIndex = function ( string, index ) {
            return string.replace( /[0-9]+(?!.*[0-9])/, function( match ) {
                return index;
            } );
        }
	}
);
