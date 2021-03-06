import $ from 'jquery';
import 'password-strength-meter';

const passwordOkayScore = 35;

$(document).ready(function() {
	let options = {
	  shortPass: 'Het wachtwoord is te kort',
	  badPass: 'Zwak; probeer letters & nummers te combineren',
	  goodPass: 'Medium; probeer speciale karakters te gebruiken',
	  strongPass: 'Sterk wachtwoord',
	  containsUsername: 'Het wachtwoord bevat de gebruikersnaam',
	  enterPass: 'Type je wachtwoord',
	  showPercent: false,
	  showText: true, // shows the text tips
	  animate: true, // whether or not to animate the progress bar on input blur/focus
	  animateSpeed: 'fast', // the above animation speed
	  username: false, // select the username field (selector or jQuery instance) for better password checks
	  usernamePartialMatch: true, // whether to check for username partials
	  minimumLength: 8 // minimum password length (below this threshold, the score is 0)
	};
	
	$('#user_add_password_first').password(options);
	$('#user_add_password_second').password(options);
	
	onPasswordScore('#user_add_password_first');
	onPasswordScore('#user_add_password_second');
	
	$('#reset_password_plainPassword_first').password(options);
	$('#reset_password_plainPassword_second').password(options);
	
	onPasswordScore('#reset_password_plainPassword_first');
	onPasswordScore('#reset_password_plainPassword_second');	
});

function onPasswordScore(id) {
	$(id).on('password.score', (e, score) => {
		if (score > passwordOkayScore) {
			$('button[name=submit]').prop("disabled", false);
		} else {
			$('button[name=submit]').prop("disabled", true);
		}
	});
};
