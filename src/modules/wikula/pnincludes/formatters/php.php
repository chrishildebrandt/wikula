<?php
/**
 * PHP language file for Wikka highlighting (uses PHP built-in highlighting).
 */

if (isset($method) && $method == 'diff') {
	// save output buffer and restart with clean buffer
	$dummy = ob_get_clean(); ob_start();
	// replace diff-tags to prevent highlighting these html-entities!
	$code = str_replace(array('&pound;&pound;', '&yen;&yen;'), array('��', '��'), $code);
}

highlight_string($code);

if (isset($method) && $method == 'diff') {
	// get highlighting output
	$listing = ob_get_clean(); ob_start();

	// render diff tags
	$listing = preg_replace('/��<\/font>/', '</font>��', $listing);
	$listing = preg_replace('/��(.*?)��/', '<span class="additions">\\1</span>', $listing);
	$listing = preg_replace('/��<\/font>/', '</font>��', $listing);
	$listing = preg_replace('/��(.*?)��/', '<span class="deletions">\\1</span>', $listing);
	// write original output and revised highlighting back to fresh buffer
	print $dummy.$listing;
}
