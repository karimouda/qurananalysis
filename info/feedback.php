<?php

?>
<p class='info-page-text'>

	<h1 class='info-page-section-title'>Your Feedback</h1> 
	
	<input id='feedback_user_name'  name='name' value='' placeholder="Name"></input>
	<br>
	<input id='feedback_user_email'  name='email' value='' placeholder="Email"></input>

	<div id='feedback_type_area' >
	<input id='radio_feedback_error' type="radio" name="feedback_type" value="FEEDBACK" checked="true">
	<label for="radio_feedback_error">Feedback/Error</label>
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
