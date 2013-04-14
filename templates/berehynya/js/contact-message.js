window.addEvent('domready', function(e) {
    if($('contact-message')) {
        SqueezeBox.initialize({});
        SqueezeBox.open( $('contact-message'), {
            handler: 'adopt',
            shadow: true,
            overlayOpacity: 0.5,
            size: {x: 600, y: 100},
            onOpen: function(){
                $('contact-message').setStyle('visibility', 'visible');
            }
        });
    }
});