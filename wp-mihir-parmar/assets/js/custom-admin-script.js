jQuery( function($){
	jQuery( 'body' ).on( 'click', '.wpc-upload', function( event ){
		event.preventDefault();
		
		const button = $(this)
		const imageId = button.next().next().val();
		
		const customUploader = wp.media({
			title: 'Insert image',
			library : {
				type : 'image'
			},
			button: {
				text: 'Use this image' 
			},
			multiple: false
		}).on( 'select', function() { 
			const attachment = customUploader.state().get( 'selection' ).first().toJSON();
            const imageName = attachment.title;

			button.removeClass( 'button' ).html( '<img src="' + attachment.url + '">');
			button.next().show(); 
			button.next().next().val( attachment.id ); 
            button.next().next().next().val(imageName); 

		})
		
		// already selected images
		customUploader.on( 'open', function() {

			if( imageId ) {
			  const selection = customUploader.state().get( 'selection' )
			  attachment = wp.media.attachment( imageId );
			  attachment.fetch();
			  selection.add( attachment ? [attachment] : [] );
			}
			
		})

		customUploader.open()
	
	});
	// on remove button click
	jQuery( 'body' ).on( 'click', '.wpc-remove', function( event ){
		event.preventDefault();
		const button = jQuery(this);
		button.next().val( '' ); // emptying the hidden field
		button.hide().prev().addClass( 'button' ).html( 'Upload image' ); // replace the image with text
	});
});