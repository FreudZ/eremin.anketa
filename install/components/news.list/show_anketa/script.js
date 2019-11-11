$(document).ready(function(){

$(".rb-tab").click(function(){
  //Spot switcher:
  let variant = $(this).data('value');
  let param_id = $(this).data('param-id');
  let question_id = $(this).data('question-id');

  $('input:radio[name=QUESTION_'+param_id+'_'+question_id+']').attr('checked',false);
  $('input:radio[name=QUESTION_'+param_id+'_'+question_id+']')[variant-1].checked = true;
  $(this).parent().find(".rb-tab").removeClass("rb-tab-active");
  $(this).addClass("rb-tab-active");
  $(this).parents('td').removeClass('bg-danger');
});


$('input:text.required').on('input change paste', function(){
  	if($(this).val().length){
	  $(this).parents('.form-group').removeClass('has-error');
  	}else{
	  $(this).parents('.form-group').addClass('has-error');
  	}
});

  $('#submit_button').on('click', function(){
    checkAndSubmitForm();
  });

    $('#addData_button').on('click', function(){
    addTestData();
  });


});

function checkForm(){
  var error = false;
  $('.error_text').addClass('hidden');
  	$('#anketa td').removeClass('bg-danger');
  $('.rb-tab').each(function(){
		 var option_name = $(this).find('.rb-txt').data('option-name');
		 var $option = $('input:radio[name='+option_name+']:checked');
		  if (!$option.val()){
		  	error = true;
			 $(this).parents('td').addClass('bg-danger');
		  }



  });

  $("input:text.required").each(function(indx, element){
    if(!$(element).val().length){
    	$(element).parents('.form-group').addClass('has-error');
		error = true;
    }
  });

   return !error;
}

function getRandomInRange(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
  //return Math.floor(Math.random() * (max - min)) + min;
}



function addTestData(){
   $('.rb-tab').each(function(){
		 var option_name = $(this).find('.rb-txt').data('option-name');
		 var $option = $('input:radio[name='+option_name+']');
		 var value = getRandomInRange(0, 4);
		 var param_id = $(this).data('param-id');
		 var question_id = $(this).data('question-id');
		 $($option).eq(value).attr("checked",true);
		 $('.cont_'+param_id+'_'+question_id+'_'+value).click();


  });

 $('input:text, textarea').each(function(){
	 $(this).val('test data #'+getRandomInRange(0, 25536));


 });

}



function submitForm(){
   	  var msg   = $('#anketa').serialize();
        $.ajax({
          type: 'POST',
          url: TEMPLATE_FOLDER+'/ajax/anketa.php',
          data: msg,
          success: function(data) {
          	console.log(data);
          	if(data == 'success'){
          		$.fancybox( $('.success_message') );
				   if(REDIRECT.length>0){
					$('.success_message').fancybox({ onClosed : function(){ document.location=REDIRECT; } });

					setTimeout(
					function(){
						$.fancybox.close();
						window.top.location.href=REDIRECT;
						}
					, 3000);

					}


          	}
            //$('#results').html(data);
          },
          error:  function(xhr, str){
	    alert('Возникла ошибка: ' + xhr.responseCode);
          }
        });
}

function checkAndSubmitForm(){
  if(checkForm()){
  	$('.error_text').addClass('hidden');
	submitForm();
  } else {
  	$('.error_text').removeClass('hidden');
  }

}