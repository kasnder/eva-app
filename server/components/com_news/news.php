<?php
defined('DIR') or die;

class Component extends ComponentTemplate {
	protected static $_defaultView = 'json';

	function launch () {
		// Get news from Yahoo Server
		$url = 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20rss%20where%20url%3D%22http%3A%2F%2Fotto-pankok-schule.de%2Findex.php%2Fneuigkeiten%3Fformat%3Dfeed%26type%3Drss%22&format=json';
		$jsonString = Common::getContent($url);
		if (!$jsonString) return null;

		// Migrate from former Google RSS fetcher format
		$search = array('"pubDate":', '"description":');
		$replace = array('"publishedDate":', '"content":');
		$jsonString = str_replace($search, $replace, $jsonString);

		// Parse json
		$json = json_decode($jsonString, true);
		array_walk($json['query']['results']['item'], function(&$value, $key) {
		    $value['contentSnippet'] = mb_substr(strip_tags($value['content']), 0, 100);
		});

		// Check for entries
		if (Common::getRequest('contentOnly') == 1 && isset($json['query']['results']['item'])) {
			return $json['query']['results']['item'];
		}

		// Return result
		return $json;
	}
}