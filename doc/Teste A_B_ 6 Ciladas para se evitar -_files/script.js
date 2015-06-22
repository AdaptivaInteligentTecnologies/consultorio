;(function ($) {
    "use strict";
    $(function () {
        var placeholder = new ApikiGravityFormPlugin();
    });

    var ApikiGravityFormPlugin = function(){
        this.init();
    };

    ApikiGravityFormPlugin.prototype.init = function() {
        this.insertPlaceholders();
    };

    ApikiGravityFormPlugin.prototype.insertPlaceholders = function() {
        ( window.gravityFormPlaceholders || [] ).forEach( jQuery.proxy( this, 'iterateGravityInputs' ) );
    };

    ApikiGravityFormPlugin.prototype.iterateGravityInputs = function(inputs) {
        if ( !inputs && !inputs.length ) {
            return;
        }
        
        this.iteratePlaceholders( inputs );
    };

    ApikiGravityFormPlugin.prototype.iteratePlaceholders = function(elements) {
        elements.forEach(function( element ) {
            $( element.input ).attr( 'placeholder', element.placeholder );
            if ( $.fn.placeholder ) {
                $( element.input ).placeholder();
            }
        });
    };
    
})(jQuery);
