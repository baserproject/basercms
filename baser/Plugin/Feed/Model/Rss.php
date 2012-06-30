<?php
/**
 * Created: Wed Sep 06 18:03:26 CEST 2006
 * 
 * This Model allows you to parse a given RSS 2.0 feed and have it returned in a big 
 * array.
 * 
 * PHP versions 5
 *
 * Copyright (c) Felix Geisendrfer <info@fg-webdesign.de>
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright		Copyright (c) 2006, Felix Geisendrfer. 
 * @link			http://www.fg-webdesign.de/
 * @link			http://www.thinkingphp.org/ 
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Include files
 */
App::import("Model","Feed.WebModel");
/**
 * rss
 * 
 * @package baser.plugins.feed.models
 */
class Rss extends WebModel {
/**
 * name
 * 
 * @var string 
 * @access public
 */
    var $name = 'Rss';
/**
 * cacheExpires
 * 
 * @var string 
 * @access public
 */
    var $cacheExpires = '+2 hours';
/**
 * cacheFolder
 * 
 * @var string 
 * @access public
 */
    var $cacheFolder = 'web/rss';
/**
 * useDbConfig
 * 
 * @var string
 * @access public
 */
    var $useDbConfig = null;
/**
 * findAll
 * 
 * @param string $feedUrl
 * @param int $limit
 * @param string $cacheExpires
 * @return array
 * @access public
 */  
	function findAll($feedUrl, $limit = 10, $cacheExpires = null)
    {
		
        if (empty($feedUrl))
            return array();

        $feed = $this->__parseRSS($this->__getRawRSS($feedUrl, null, $cacheExpires));       
        
        if (isset($feed['Error']))
            return $feed;
        
        if (count($feed['Items']>$limit))
        {                                
            $feed['Items'] = array_slice($feed['Items'], 0, $limit);
        }

        return $feed;
    }
/**
 * getRawRSS
 * 
 * @param string $feedUrl
 * @param array $vars
 * @param string $cacheExpires
 * @return string
 * @access private
 */    
	function __getRawRSS($feedUrl, $vars = array(), $cacheExpires = null)
    {
		
        $url = $feedUrl;
        
        $cachePath = $this->cacheFolder.$this->__createCacheHash('.rss', $url, $vars);

        if (empty($cacheExpires))
            $cacheExpires = $this->cacheExpires;
            
        if (empty($vars))
            $vars = array();
                    
        $rssData = cache($cachePath, null, $cacheExpires);
        
        if (empty($rssData))
        {
            $rssData = cache($cachePath, $this->httpGet($url, $vars));
        }
        
        return $rssData;
		
    }
    
 /**
 * A simple function for parsing RSS data. Only returns Items for now.
 *
 * @param string $data
 * @return array 
 * @access private
 */
    function __parseRSS($data)
    {
		
        if (empty($data))
            return array();
    
        $regex = '/\<rss.+version="(.+)".*\>/iUs';
        
        preg_match($regex, $data, $match);
        if (empty($match))
            return array('Error' => 'No valid feed (no feed version found).');
        
        list($raw, $version) = $match;
                
        if (empty($version))
            $version = '2.0';

        // Check if we have a valid version number
        if (!preg_match('/^[0-9.]+$/iUs', $version))
        {
            return array('Error' => '"'.$version.'" is no valid RSS version.');
        }
                    
        $rssFunction = '__parseRSS_'.str_replace('.', '_', $version);
                   
        if (method_exists($this, $rssFunction))
        {
            return call_user_func(array(&$this, $rssFunction), $data);
        }
        else 
        {
            return array('Error' => 'No function for parsing RSS feeds of version "'.$version.'" available.');
        }
		
    }
/**
 * parseRSS_2_0
 * 
 * @param string $data
 * @return array 
 * @access private
 */    
    function __parseRSS_2_0($data)
    {

        // First thing we need to do, is to identify all html/otherwise formated contents
        preg_match_all('/\<\!\[CDATA\[(.+)\]\]\>/iUs', $data, $cdata, PREG_SET_ORDER);


        // Create the md5 hash of the data to parse
        $dataHash = md5($data);
        
        // Now we have to replace them with something that won't confuse our parser, but still keep the array containing their original content
        // [[CDATA:$dataHash:$cdataNum]] should be pretty unique, so we don't have to deal with errors in an rss feed that talks about this replacment
        // method.
        foreach ($cdata as $cdataNum => $cdataItem)
        {
            $data = str_replace($cdataItem[0], '[[CDATA:'.$dataHash.':'.$cdataNum.']]', $data);
        }            
        
        
        // Let's get the information about the channel
        $regex = '/\<channel\>(.+)\<item\>/iUs';
        preg_match($regex, $data, $match);
        if (!empty($match))
        {
            list($raw, $channel) = $match;
            
            $channel = $this->__getNodeFields($channel, $cdata, $dataHash, 'channel');
        }
        else 
            $channel = array();
        
        // This will get us a list with all Items contained in the feed
        $regex = '/\<item\>(.+)\<\/item\>/iUs';
        
        $matchCount = preg_match_all($regex, $data, $matches, PREG_SET_ORDER);                
        if (empty($matchCount))
        {
            // No items? Nothing to parse.
            $matches = array();        
        }
        else 
        {                            
            $items = array();
            
            // Loop through all Item Matches
            foreach ($matches as $itemNr => $item)
            {   
                // Find all fields in our Item           
                
                $items[$itemNr] = $this->__getNodeFields($item[1], $cdata, $dataHash);
            }
        }
    
        // Return everything
        return array('Channel' => $channel,
                     'Items' => @$items);
		
    }    
/**
 * getXMLNodeAttributes
 * 
 * @param string $rawFields
 * @param string $cdata
 * @param string $dataHash
 * @param string $type
 * @return string 
 * @access private
 */    
    function __getNodeFields($rawFields, $cdata = null, $dataHash = null, $type = null)
    {
		
        // Don't ask - it works. No seriously, I spent a lot of time and thought on this regex
        // if you are interested in how it works feel free to contact me. In case you wonder about
        // the \x00's, that's an optimization trick to generate a character set that matches new lines
        // but doesn't require the /s modifier.
        $fieldRegex = '/\<(.+)( [^\x00]*)?\>([^\x00]*)\<\/\\1\>|\<(.+)( [^\x00]*)?\/\>/U';
            
        preg_match_all($fieldRegex, $rawFields, $fieldMatches, PREG_SET_ORDER);
        
        // Loop through those fields
        foreach ($fieldMatches as $fieldMatch)
        {
            // Assign the preg_match_all contents to a couple of variables
            if (count($fieldMatch)==4)
                list($raw, $field, $attributes, $value) = $fieldMatch;
            else 
            {
                // This is for <nodes ... /> that don't have enclosed content
                list($raw, , , ,$field, $attributes) = $fieldMatch;
                $value = null;
            }
             
            // The child image in channel has child elements in RSS, so let's make sure we parse them too
            if ($type=='channel' && $field=='image')
            {
                $value = $this->__getNodeFields($value, $cdata, $dataHash);
            }
            else 
            {
                // Find CDATA replaced stuff and but it back in.
                preg_match_all('/\[\[CDATA:'.$dataHash.':([0-9]+)\]\]/iUs', $rawFields, $cdataDummies, PREG_SET_ORDER);
                foreach ($cdataDummies as $cdataDummy)
                {
                    // Replace CDATA dummies with the actual contents of the cdata field
                    $value = str_replace($cdataDummy[0], $cdata[($cdataDummy[1])][1], $value);
                }
            }
            
            // Parse the attributes contained in our Node / ItemField
            $attributes = $this->__getXMLNodeAttributes($attributes);
        
            // Add our news Node to the list of Items.
            $fields[$field] = array('value' => $value,
                                    'attributes' => $attributes);
        }    
        
        if (!isset($fields))
            $fields = $rawFields;
        
        return $fields;
		
    }
/**
 * getXMLNodeAttributes
 * 
 * @param string $attributesData
 * @return array
 * @access private 
 */    
    function __getXMLNodeAttributes($attributesData)
    {
		
        if (empty($attributesData))
            return array();
    
        preg_match_all('/ ([^ \r\n]+)=(["\'])(.+)\\2/iUs', $attributesData, $attributeMatches, PREG_SET_ORDER);
        
        if (empty($attributeMatches))
            return array();
            
        $attributes = array();
        
        foreach ($attributeMatches as $attribute)
        {
            list($raw, $attributeKey, $enclosure, $attributeValue) = $attribute;
            $attributes[] = array($attributeKey => $attributeValue);
        }
        
        return $attributes;
		
    }
	
}

