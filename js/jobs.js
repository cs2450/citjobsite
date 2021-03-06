jQuery(document).ready(function($) {
	/////////////////
	//  FUNCTIONS  //
	/////////////////
	// satisfies specifications 1.1.2.1 and 2.1.2.1
	function validateEmail($email) {
    	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    	if( !emailReg.test( $email ) ) {
        	return false;
    	} else {
        	return true;
    	}
	}
	
	//////////////////////////////////////////////////////////////////////
	// 	LOGIN.PHP HANDLERS FOR SETTINGS WHEN LOGIN FORMS ARE SUBMITTED  //
	//////////////////////////////////////////////////////////////////////
	// statisfies specification 9.2.3
	$('#loginButton').click(function () {
        $(".error").hide();
             var hasError = false;
             var passwordVal = $("#password").val();
             var email = $("#email").val();
			        
             if (email == '') {
                 $(".formBottom").after('<span class="error">Please enter a valid email.</span>');
                 hasError = true;
             } else if (passwordVal == '') {
             
             $("#loginForm").before('<span class="error">Please enter a password.</span>');
             hasError = true;
             }

             if(hasError == true) {$('.error').fadeIn();  return false;}
	
	});
	
	/////////////////////////////////////////////////
	//	REGISTER.PHP HANDLERS FOR FORM VALIDATION  //
	/////////////////////////////////////////////////
	// satisfies specification 1.2 1.2.1 2.2 2.2.1	
	$('#register_button').click(function () {
		$(".error").remove();
             var hasError = false;
			 
		     // Get register form values for validation
			 var email = $('input[name=register_email]').val();
			 var email_confirm = $('input[name=confirm_email]').val();
			 var pass = $('input[name=register_password]').val();
			 var pass_confirm = $('input[name=confirm_password]').val();
			 var name = $('input[name=name]').val();
			 var phone = $('input[name=phone]').val();
			 
             if (email == '' | !validateEmail(email)) {
                 $("#form_container").before('<span class="error">Please enter a valid email.</span>');
                 hasError = true;
             } 
			 else if (email != email_confirm) {
                 $("#form_container").before('<span class="error">Emails do not match.</span>');
				 hasError = true;
			 }
			 else if (pass == '') {
				 $("#form_container").before('<span class="error">Please enter a password.</span>');
             	hasError = true;
             }
			 else if (pass.length < 6) {
				 $("#form_container").before('<span class="error">Password length must be at least 6.</span>');
				hasError = true;
			 }
			 else if (pass != pass_confirm) {
				 $("#form_container").before('<span class="error">Passwords do not match.</span>');
				hasError = true;
			 }
			 else if (name == '') {
				 $("#form_container").before('<span class="error">Please enter your name.</span>');
				hasError = true;
			 }
			 else if(phone == '' || phone.length != 10) {
				$("#form_container").before('<span class="error">Please enter a valid phone number.</span>');
				hasError = true;
			}
			
			else if(!$('#student_radio').is(":checked") && !$('#employer_radio').is(":checked")) {
				$('#form_container').before('<span class="error">Please select your registration type.</span>');
				hasError = true;
			}
			
			else if($('input[name=company]').val() == '') {
				$('#form_container').before('<span class="error">Please enter your company name.</span>');
				hasError = true;
			}
			else if($('textarea[name=description]').val() == '') {
				$('#form_container').before('<span class="error">Please tell us about your company.</span>');
				hasError = true;
			}
	
			// return false for an error
             if(hasError == true) {$('.error').fadeIn('slow'); return false;}
			 
			 else {document.register.submit();}
	
	});
	
	// Register.php employer radio button click handler (displays company name input box), hide if student radio button is clicked
	
	$('#employer_radio').click(function () {
		$('label[for=company]').remove();
		$('input[name=company]').next().remove().end().remove();
		$('label[for=description]').remove();
		$('textarea[name=description]').next().remove().end().remove();
		$('div[id=formspacer]').remove();
		
		$('<label for="company"><strong>Company Name:</strong></label>\n<label for="description"><strong>Company Description:</strong></label><div id="formspacer" style="height: 40px;"></div>').insertAfter('label[for=confirm_password]').hide().fadeIn('slow');
		$('<input type="text" name="company" style="display:inline; margin-top: 0px;" /><strong>Please fill out</strong>\n<textarea rows="5" cols="50" name="description" style="display:inline; margin-top: 0px; margin-left: 10px; border: 1px solid green;"></textarea>').insertAfter('input[name=confirm_password]').hide().fadeIn('slow');
		$('input[name=company]').css('border', '1px solid green');
	});
	
	$('#student_radio').click(function () {
		$('label[for=company]').remove();
		$('input[name=company]').next().remove().end().remove();
		$('label[for=description]').remove();
		$('textarea[name=description]').next().remove().end().remove();
		$('div[id=formspacer]').remove();
	});
	
	/////////////////////////////////////////////////////////////////////////////
	//	SKILLS.PHP HANDLERS FOR COLLAPSIBLE MENUS AND ADDING NEW SKILL FIELDS  //
	/////////////////////////////////////////////////////////////////////////////
	/*$('.sub').hide();
	
	// Expand skills sections when they are clicked on
	$('.sections').click(function () {
	
		if(this.id == 'cs')
			$('#cs_list').slideToggle();
		
		else if(this.id == 'it')
			$('#it_list').slideToggle();
		
		else if(this.id == 'vt')
			$('#vt_list').slideToggle();
		
		else
			return false;
	
	});*/
	var num_other_fields = 0;
	if($('#other_count').val()){
		num_other_fields = $('#other_count').val();
	}
	
	// Add other skill fields
	$('#add').click(function () {
		$('#other_skills').append('<br /><input type="text" id="other'+ ++num_other_fields + '"  name="other'+ num_other_fields + '" /><input type="button" value="Remove" id="skill_remove'+ num_other_fields + '" class="skill_remove" />');
	});
	// Remove other skill field
	$('.skill_remove').live("click", function () {
		this.previousSibling.previousSibling.id = 'abr';
		var element = document.getElementById(this.previousSibling.id);
		element.parentNode.removeChild(element);
		element = document.getElementById(this.id);
		element.parentNode.removeChild(element);
		element = document.getElementById('abr');
		element.parentNode.removeChild(element);
	});
	
	//////////////////////////////////////////////////////////////////////////////////
	//	RESUME.PHP HANDLERS FOR FILE VALIDATION BEFORE UPLOADING TO THE SERVER	//
	//////////////////////////////////////////////////////////////////////////////////
	
	$('#upload_button').click(function () {
		var file = $('input[type=file]').prop('files')[0];
		
		// Check if the file is empty
		if(!file && $('.error').length == 0)
		{
			$('input[type=file]').after('<span class="error">Please Select a File</span>');
			$('.error').fadeIn();
			return false;
		}
		
		// Check the file type. If it isn't PDF throw an error
		var mime = file.type;
		
		if(mime != 'application/pdf')
		{
			$('.error').remove();
			$('input[type=file]').after('<span class="error">Invalid file type. PDF files only.</span>');
			$('.error').fadeIn();
			return false;
		}
		
		// Check the file size. > 5mb throw and error
		var size = file.size;
		
		if(size > 5000000)
		{
			$('.error').remove();
			$('input[type=file]').after('<span class="error">File is too large. 5 MB maximum.</span>');
			$('.error').fadeIn();
			return false;
		}
		
		$('form[name=upload]').submit();
	});
	
	///////////////////////////////////////////////
	// Edit company information handler for logo //
	///////////////////////////////////////////////
	$('#edit_company_submit').click(function () {
		var file = $('input[name=img_upload]').prop('files')[0];
		
		// Check the file type. If it isn't JPG, GIF, or PNG throw an error
		var mime = file.type;
		
		if(mime != 'image/jpeg' && mime != 'image/gif' && mime != 'image/png')
		{
			$('.error').remove();
			$('input[type=file]').after('<span class="error" style="position:absolute;">Invalid file type. JPG, GIF, or PNG only.</span>');
			$('.error').fadeIn();
			return false;
		}
		
		$('form[name=edit_company]').submit();
	});
	
	// Hide the contact information div if clicked
	$('#hide_contact').click(function () {
		$('#contact_info').slideToggle();
	});

	/////////////////////////////////////////////////////////////
	//	HEADER.PHP STICKY HANDLER FOR LOGIN AND MENU BOXES //
	/////////////////////////////////////////////////////////////
	$(window).scroll(function () {
		if ($(window).scrollTop() > $('#sticky-anchor').offset().top)
			$('.sticky-handle').addClass('stick')
		else
			$('.sticky-handle').removeClass('stick');
	});
	/////////////////////////////////////////////////////////
	//	POST_JOB.PHP REQUIRED FIELDS HANDLER
	/////////////////////////////////////////////////////////
	$('#submit_job_button').click(function () {
		// Get register form values for validation
		var title = $('input[name=title]').val();
		var desc = $('input[name=job_description]').val();
		
		if (title == '')
			return false;
		else if (desc == '')
			return false;
		return true;
	});

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	JOB_DETAIL.PHP RENEW BUTTON CLICK HANDLER TO HAVE THEM SELECT AN AMOUNT OF TIME TO RENEW ON	//
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	$('#renew').one('click', function () {
		$(this).after('<select name="lifetime" id="update_renewal">'+
				'<option>Select one</option>'+
				'<option value="6">6 Months</option>'+
				'<option value="3">3 Months</option>'+
				'<option value="1">1 Month</option>'+
			'</select>');

		$('#update_renewal').change(function () {
			$.ajax({
				url: 'php/post_job_script.php',
				data: {
					id: $('input[name=job_id]').val(),
					action: 'renew',
					lifetime: $('#update_renewal').val(),
				},
				success: function (data) {
					alert('Job renewed successfully!');
					$('#expires').html('Expires: '+data);
					$('#update_renewal').remove();
				}			
			});	
		});
		
		return false;
	});

	//////////////////////////////////////////////////////////////////////////////////
	//	JOB_DETAIL.PHP UNFILL BUTTON CLICK AJAX. RESETS STATUS TO ACTIVE	//
	//////////////////////////////////////////////////////////////////////////////////
	$('input[name=unfill]').click(function () {
		$.ajax({
			type: 'GET',
			url: 'job_detail.php',
			data: {
				'job': $('input[name=job_id]').val(),
				'unfill': true,
			},
			success: function (data) {
				alert('Job is active again.');
				window.location.reload(true);
			}
		});
	});
});
