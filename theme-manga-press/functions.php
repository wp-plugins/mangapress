<?php
/*
   file: functions.php
 
			wp_calendar_icon()
 
	(c) 2008 Jessica C Green
 
	This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 
			-----------------------------------------
 
	This function should be used inside a Loop (custom or The Loop)
	$date_stamp is a value passed by $post->post_date, I've tried using
	other methods, including a value from get_the_time(), but I never got
	a correct date through this method, so I fell back on using $post->post_date.
*/
function wp_calendar_icon($date_stamp, $style = 'medium') {
 
	// first thing we do is convert the string into a time stamp
	$time_stamp = strtotime($date_stamp);
	$title = date('F jS Y', $time_stamp); // we need this for the title
	//
	// Now, we use extract() to explode the keys from the getdate array
	// into seperate variables
	extract(getdate($time_stamp));
 
	$style = " ".$style; // let's add an extra space to $style
 
	echo "	<span class=\"cssCalendar$style\" title=\"$title\">\n";
	if ($style == ' medium') { 
		echo "		<span class=\"day\">".substr($weekday, 0, 3)."</span>\n";
		echo "		<span class=\"cssCalInner\">\n";
		echo "			<span class=\"date\">$mday</span>\n";
		echo "			<span class=\"month\">".substr($month, 0, 3)."</span>\n";
		echo "			<span class=\"year\">$year</span>\n";
		echo "		</span>\n";
	} elseif ($style == ' small') {
		echo "		<span class=\"month\">".substr($month, 0, 3)."</span>\n";
		echo "		<span class=\"cssCalInner\">\n";
		echo "			<span class=\"date\">$mday</span>\n";
		echo "		</span>\n";
	}
	echo "	</span>\n";
}

add_action('wp_footer', 'add_credits');

function add_credits() {
?>
<br />Silk Icon set from <a href="http://www.famfamfam.com/lab/icons/silk/" rel="website credits">FamFamFam</a><br />
Manga+Press Wordpress Theme by <a href="http://designs.dumpster-fairy.com/">Dumpster Fairy Designs</a><br />
<?
}
?>