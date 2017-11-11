<?php
defined('DIR') or die;

class Component extends ComponentTemplate
{

    protected static $_defaultView = 'json';

    function launch()
    {
        // Get news from Google Server
        $url
                    = 'https://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=20&q=http%3A%2F%2Fotto-pankok-schule.de%2Fneuigkeiten%3Fformat%3Dfeed%26type%3Drss';
        $jsonString = Common::getContent($url);
        if ( ! $jsonString) {
            return null;
        }

        // Parse json
        $json = json_decode($jsonString, true);

        // Check for entries
        if (Common::getRequest('contentOnly') == 1 && isset($json['responseData']['feed']['entries'])) {
            return $json['responseData']['feed']['entries'];
        }

        // Return result
        return $json;
    }
}