<?php
// GENERAL SETTINGS

// fill in below the Questions between ' and ' . You can add or delete questions in the array
$questions = [
	1 => 'Which of the following is a serverside scripting language?', // question 1
	2 => 'Which one does not belong in the list?', // question 2...and so on below
	3 => 'How many bits contains 1 byte?',
	4 => 'How many values can an array contain?',
	5 => 'What means the word "iteration" in ict?',
	6 => 'A foreach is mostly used for:',
	7 => '$variable = true; is an example of a:',
	8 => 'The programming language Phyton was introduced by:',
	9 => 'The term SSL as an encryption protocol means:',
	10 => 'Which of the following is not a Linux distribution:',
	// add as much as you want	
	];
	
// each number of the question corresponds to the number of the options	
// fill in below the options for each question between ' and ' . You can add more options for each question  by adding extra 'value' to it
$options = [
	1 => ['HTML', 'PHP', 'CSS', 'Javascript'], // options for question 1
	2 => ['.jpg', '.png', '.csv', '.gif'], // options for question 2...and so on below
	3 => ['2', '4', '8', '12'],
	4 => ['max 24', 'max 512', 'max 1024', 'infinite'],
	5 => ['compare 2 values with each other', 'to set a condition', 'a repeat inside a loop', 'to count the number of values in an array'],
	6 => ['walk through all the values of an array', 'make a statement inside a condition', 'to calculate a value', 'to determine how many characters are in a string'],
	7 => ['integer', 'array', 'string', 'boolean'],
	8 => ['Linus Torvalds', 'Guido van Rossum', 'Rasmus Lerdorf', 'Bill Gates'],
	9 => ['Single Secure Loop', 'Simple Seperate link', 'Spam Solution Link', 'Secure Socket Layer'],
	10 => ['Ubuntu', 'Windows', 'Fedora', 'openSUSE'],
	// add as much as you want	
];

// EXPIRATION SETTINGS
$expire = false; // if you want an expiration date for the examination; set to 'true'; otherwise 'false'. When true: fill in date below
$expirationtime  = strtotime("January 22, 2020 11:35:00"); // set an expiration date


// DO NOT EDIT BELOW
$currenttime = strtotime("now");


