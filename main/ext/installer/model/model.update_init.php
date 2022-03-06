<?php
/*
Name: Steph Kennedy
Date: 3/12/21
Comment: Unlike some of the other loops we've been doing this one isn't a dynamic array because we're basically going to load a different model based on which step we're on.

If this doesn't work well, we might add an additional mode to the ajax_loop_interface class to allow us more freedom to abstract processes like this into a loopable process.
*/
return [
	'download',
	'extract',
	'database',
	'move_files'
];