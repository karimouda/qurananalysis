<?php

?>
<p class='info-page-text'>

	<h1 class='info-page-section-title'>Your Feedback</h1> 
	
	


	<input id='feedback_user_name'  name='name' value='' placeholder="Name"></input>
	<br>
	<input id='feedback_user_email'  name='email' value='' placeholder="Email"></input>

	<div id='feedback_type_area' >
	<input id='radio_feedback_feedback' type="radio" name="feedback_type" value="FEEDBACK" checked="true">
	<label for="radio_feedback_feedbac">Feedback</label>
	<br>
		<input id='radio_feedback_error' type="radio" name="feedback_type" value="BUG">
	<label for="radio_feedback_error">Error/BUG</label>
	<br>
	<input id='radio_feedback_idea' type="radio" name="feedback_type" value="IDEA">
	<label id='radio_feedback_idea' for="radio_feedback_idea">Idea</label>
	</div>

	<textarea id='feedback_user_text'  name='feedback_text' >Your Feedback</textarea>
	<br>
	<input type='button'  name='submit' value='Submit' onclick="submitFeedback()" ></input>
	<span id='feedback-status' class='general-loading-color'></span>
	<br>
	<br>
</p>
	<br>
	<hr/>
	<br>
	<h1 class='info-page-section-title'>Public Feedback</h1> 
	<br>
<div class="fb-comments" data-href="http://qurananalysis.com/info/index.php?page=feedback" data-numposts="5"></div>
	
