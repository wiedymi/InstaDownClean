
jQuery(document).ready(function($) {
	if($('.csbuttons').length > 0) {
        $('.csbuttons').cSButtons();
    }
	$('.menu_block i').click(function(event) {
		$('.menu_block').toggleClass('active');
		$('.dropdown_menu').fadeToggle(100);
	});
	$(document).on('click', function() {
	    if (!$(event.target).closest(".menu_block, .dropdown_menu").length) {
	        $(".dropdown_menu").fadeOut(100);
	        $(".menu_block").removeClass('active');
	    }
	});
	
	$('.download_form').on('submit', function(e) {
		e.preventDefault();

		$.ajax({
		  	type: 'POST',
				url: '/instagram/ajax',
		  	data: {
		  		source_url: $('input[name="source_url"]').val(),
		  		action: 'get_download_link',
		  	},
		  	success: function(response) {

					var response = JSON.parse(response);
					
		  		if (response['status'] == true) {
		  			$('.error_block').fadeOut();
		  			$.fileDownload(response['url']);	
		  			
		  			//downloadPhoto(response['url'], response['username'], response['id']);  				
		  			$('input[name="source_url"]').val('');
		  		} else {
		  			$('.error_block').text(response['error']);
		  			$('.error_block').fadeIn();
		  		}
		  		hideKeyboard();
  			},
		});
	});

	var regMail = /^[\w\.\d-_]+@[\w\.\d-_]+\.\w{2,4}$/i;
	$('body').on('click', '#contact-us-form button[type="submit"]', function(e) {
		e.preventDefault();
		var _this = $(this).closest('form'),
			name = _this.find('input[name="name"]'),
			email = _this.find('input[name="email"]'),
			loadingPlaceForm = $('#loading-place-form'),
			text = _this.find('textarea[name="text"]');

	    var datastring = _this.serialize();

		if(name.val().length >= 2) {
			name.removeAttr('style');	
		} else {
			name.css({'border-color':'rgb(255, 0, 0)'});
			setTimeout(function(){ name.removeAttr('style'); }, 1000);
		}

		if(email.val().match(regMail)) {
			email.removeAttr('style');
		} else {
			email.css({'border-color':'rgb(255, 0, 0)'});
			setTimeout(function(){ email.removeAttr('style'); }, 1000);
		}

		if(text.val().length >= 5) {
			text.removeAttr('style');
		} else {
			text.css({'border-color':'rgb(255, 0, 0)'});
			setTimeout(function(){ text.removeAttr('style'); }, 1000);
		}
		
		if(name.val().length >= 2 &&
		   email.val().match(regMail) &&
		   text.val().length >= 5) {

		    $.ajax({
				type: 'POST',
				url: '/instagram/mail',
				data: datastring,
				cache: false,
				success: function(response){
					if(response == 'ok') {
						$('#contact-us-form').addClass('hidden');
						$('#contact-us-thanks').removeClass('hidden');
					} else {
						//
					}
					loadingPlaceForm.html('');
				},
				beforeSend: function() {
					loadingPlaceForm.html('<span class="loading-rolling"></span>');
				}
		    });
		}

	});
});

function downloadPhoto(url, username, id) {
    var form = $('<form action="/ajax" method="post">' +
                    '<input type="hidden" name="download" value="true">' +
                    '<input type="hidden" name="url" value="' + url + '">' +
                    '<input type="hidden" name="username" value="' + username + '">' +
                    '<input type="hidden" name="id" value="' + id + '">' +
                 '</form>');
    $('body').append(form);
    form.submit();
    form.remove();
}

function hideKeyboard() {
  //this set timeout needed for case when hideKeyborad
  //is called inside of 'onfocus' event handler
  setTimeout(function() {

    //creating temp field
    var field = document.createElement('input');
    field.setAttribute('type', 'text');
    //hiding temp field from peoples eyes
    //-webkit-user-modify is nessesary for Android 4.x
    field.setAttribute('style', 'position:absolute; top: 0px; opacity: 0; -webkit-user-modify: read-write-plaintext-only; left:0px;');
    document.body.appendChild(field);

    //adding onfocus event handler for out temp field
    field.onfocus = function(){
      //this timeout of 200ms is nessasary for Android 2.3.x
      setTimeout(function() {

        field.setAttribute('style', 'display:none;');
        setTimeout(function() {
          document.body.removeChild(field);
          document.body.focus();
        }, 14);

      }, 200);
    };
    //focusing it
    field.focus();

  }, 50);
}