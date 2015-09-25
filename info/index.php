<?php 
require("../global.settings.php");

$page = $_GET['page'];

function getTitleForPage($page)
{
	if ( $page=="about")
	{
		return "About";
	}
	else
	if ( $page=="resources")
	{
		return "Resources";
	}
	else
	if ( $page=="contact")
	{
		return "Contact";
	}
	else
	if ( $page=="credits")
	{
		return "Credits";
	}
	else
	if ( $page=="contribute")
	{
		return "Contribute";
	}
	else
	if ( $page=="feedback")
	{
	
		return "Feedback";
	}
	else
	if ( $page=="faq")
	{
		return "FAQ";
	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analysis | <?=getTitleForPage($page);?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/favicon.png">
      	 
	<script type="text/javascript">
	</script>


  </head>
  <body>
  
  
  

  
  
  <div id='header'>
			  	
     <?php 
		require("../header.php");
	 ?>
  		
  </div>
  
  
  
  <div id='main-container'>
			  
			  <table id='info-pages-main-table' >
			  <tr>
			  	<td id='info-pages-title-column'>
			  		<?php echo strtoupper($page)?>
			  	</td>
			  	<td>
			  			<?php 
			  			
							if ( $page=="about")
							{
								include("$page.php");
							}
							else 
							if ( $page=="resources")
							{
								include("$page.php");
							}
							else
							if ( $page=="contact")
							{
								include("$page.php");
							}
							else
							if ( $page=="credits")
							{
								include("$page.php");
							}
							else
							if ( $page=="contribute")
							{
								include("$page.php");
							}					
							else
							if ( $page=="feedback")
							{
								include("$page.php");
							}
							else
							if ( $page=="faq")
							{
								include("$page.php");
							}
							
						?>
			  	</td>
			  </tr>
			  
			  
			  </table>	

	</div>
			<script>

		
			
				function subscribe()
				{
						
					
						$("#subscribing").attr("class","general-loading-color");
						$("#subscribing").html("Subscribing...");
						
						var emailVal =  $("#subscriber_email").val();


						
						var nameVal =  $("#subscriber_name").val();
						var titleVal =  $("#subscriber_title option:selected").val();
						var entityVal =  $("#subscriber_entity").val();
						
						
						if ( titleVal=='')
						{
							$("#subscribing").attr("class","general-error-color");
							$("#subscribing").html("Invalid Occupation !");
							return;
						}
						
						
						
						var testEmail = /\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i;
						
						if (!testEmail.test(emailVal))
						{
							$("#subscribing").attr("class","general-error-color");
							$("#subscribing").html("Invalid email !");
							return;
						}
						
						
	
					
						$.ajaxSetup({
							url:  "<?php echo "/services/doSubscribe.php"?>",
							global: false,
							type: "POST"
							
						  });
					
							
									
							$.ajax({
								
						 	 data: 'email='+emailVal+"&title="+titleVal+"&name="+nameVal+"&entity="+entityVal,
							 timeout: 10000,
							 success: function(response){
								
											//alert(response);
								  	
								  			if ( response!="DONE")
								  			{
								  				$("#subscribing").attr("class","general-error-color");
												$("#subscribing").html(response);
								  			}
								  			else
								  			{
									  			
												$("#subscribing").attr("class","general-success-color");
												$("#subscribing").html("Thank you");
								  			}
											

								  			trackEvent('INFO_PAGES','SUBSCRIBE','','');
										
								 	 	
								  },
							 		 error: function (xhr, ajaxOptions, thrownError) {
							 		 		
								       alert(""+thrownError+"\nTry again or report this error using the Feedback page ..");
								        
								      }
								    });
						
						
						
						
						return false;
						
				}



				function submitFeedback()
				{
						
					
						$("#feedback-status").attr("class","general-loading-color");
						$("#feedback-status").html("Submitting...");
						
						var emailVal =  $("#feedback_user_email").val();

					
						var nameVal =  $("#feedback_user_name").val();
						var feedbackTextVal =  $("#feedback_user_text").val();
						var feedbackType =  $("INPUT[name='feedback_type']:checked").val();
						
						
						
						
						
						var testEmail = /\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i;
						
						if (!testEmail.test(emailVal))
						{
							$("#feedback-status").attr("class","general-error-color");
							$("#feedback-status").html("Invalid email !");
							return;
						}
						
						
	
					
						$.ajaxSetup({
							url:  "<?php echo "/services/doSubmitFeedback.php"?>",
							global: false,
							type: "POST"
							
						  });
					
							if (_gaq){_gaq.push(['_trackPageview', 'ACTION:Feedback']);}
									
							$.ajax({
								
						 	 data: 'email='+emailVal+"&feedbackText="+feedbackTextVal+"&feedbackType="+feedbackType+"&name="+nameVal,
							 timeout: 10000,
							 success: function(response){
								
											//alert(response);
								  	
								  			if ( response!="DONE")
								  			{
								  				$("#feedback-status").attr("class","general-error-color");
												$("#feedback-status").html(response);
								  			}
								  			else
								  			{
									  			
												$("#feedback-status").attr("class","general-success-color");
												$("#feedback-status").html("Thank you");
								  			}
											

								  			trackEvent('INFO_PAGES','FEEDBACK','','');
										
								 	 	
								  },
							 		 error: function (xhr, ajaxOptions, thrownError) {
							 		 		
								       alert(""+thrownError+"\nTry again or report this error using the Feedback page ..");
								        
								      }
								    });
						
						
						
						
						return false;
						
				}
					
	</script>
	
	
		<?php 
		require("../footer.php");
	?>
  </body>
</html>







