$(function() {

    $('#side-menu').metisMenu();
    $('ul.nav li a').on('click', function() { $('ul.nav li a').removeClass('active'); $(this).addClass('active'); } );

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse')
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse')
        }

        height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    })
})

function disableF5(e) { 
	
	for (propriedade in e){
		console.log(propriedade+'='+e[propriedade]);	
	}
	
	// 116 = F5
	// 82 = R
	if (  ( (e.which || e.keyCode) == 116 ) || (e.keyCode == 82 && e.ctrlKey)   ) e.preventDefault(); 
};

$(document).on("keydown", disableF5);

