//This toggles beaver build ctas wrapped in a tray-wrapper class and loads the linked content
jQuery(document).ready(function ($) {

    var mobile = checkSize(mobile);

    $( ".tray-wrapper .fl-callout-button a" ).on('touchstart click', function() {
        if ($(this).hasClass("tray-expanded")){
            //already clicked, toggle and close all
            event.preventDefault();
            closeTrays();
        } else {
            event.preventDefault();
            closeTrays();
            $this = $(this);
            $(this).find(".fl-button-text").text('READ MORE'); //toggling disabled
            var href = $(this).attr('href');
            $(this).addClass("tray-expanded");
            var dt;
            $.get(href, function (data) {
                dt = $(data).find('.fl-builder-content')[0].innerHTML;
                dt = "<div class='fl-col cur-content' style='display:none;'>" + dt + "</div>";
                //append to different places on smaller screens
                mobile ? $(".tray-expanded").closest(".fl-col").after(dt) : $(".tray-expanded").closest(".fl-col-group").append(dt);
           
                $( ".cur-content" ).slideDown( "fast", function() {
                  // Animation complete.
                  $this.addClass("tray-expanded-arrow");
                });
                $( ".cur-content" ).append( "<i class=' close-tray fl-accordion-button-icon fl-accordion-button-icon-right icon-glyph-345'></i>" );
                $(document).on('click', '.close-tray ', function(){ 
                  closeTrays();
                });
                //add class .active to show/hide arrows 
                $this
                .closest( ".fl-builder-content .fl-module-callout" )
                .addClass("active");  
                
            });
        }
    });

    $( window ).resize(function() {
       
        prev_mobile = mobile;
        mobile = checkSize(mobile);
        //on mobile many events trigger a resize, we only need to close trays when switching to and from mobile
        if (prev_mobile != mobile){
           closeTrays();
        }
    });

      //this check the screen width and  will determine where to append the content from the page
      function checkSize(mobile){
    
        var windowWidth = window.screen.width < window.outerWidth ?
        window.screen.width : window.outerWidth;
        mobile = windowWidth < 993;
        return mobile;

      }

      //this closes all trays
      function closeTrays(){
        //remove class .active to show/hide arrows 
        
        $('.tray-expanded').each(function( index ) {
            $(this).removeClass('tray-expanded tray-expanded-arrow').find(".fl-button-text").text('READ MORE');
            $(this)
            .closest( ".fl-builder-content .fl-module-callout" )
            .removeClass("active"); 
          });
        $('.cur-content').slideUp("normal", function() { $(this).remove(); } );
      }

      //this disables headline clicks on one page ctas
      $('.fl-callout-title-link').click(function (event) {

        // Don't follow the link
        event.preventDefault();
      
      
      });

      //truncate all of the call out paragraphs
      // $( ".fl-col-content .fl-callout .fl-callout-text-wrap .fl-callout-text p" ).each(function( index ) {
      //   $thistext = $(this).text();
      //   var shorttext = jQuery.trim($thistext).substring(0, 250).split(" ").slice(0, -1).join(" ") + "...";
      //   $(this).text(shorttext);
      // });
      

}); 

