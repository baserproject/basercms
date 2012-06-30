<?php
/**
 * A simple WebModel class that eases post & get requests to foreign pages
 * Thx to RosSoft (http://rossoft.wordpress.com/) for the initial curl code!
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * Author: Felix Geisendorfer (thinkingphp.org / fg-webdesign.de)
 * 
 */
/**
 * Include files
 */
/**
 * web_model
 * 
 * @package baser.plugins.feed.models
 */
class WebModel extends AppModel
{
/**
 *
 * @var boolean
 * @access public
 */
    var $useTable = false;
/**
 *
 * @var int 
 * @access public
 */
    var $connection_timeout = '30';
    
/**
 * Set this to active if security is very important for your ssl connection, however I had
 * (valid) certification that would not pass the curl libraries ssl checks.
 * 
 * @var boolean
 * @access public 
 */
    var $ssl_strict = false;
    
/**
 * This Model actually doesn't do any caching itself, this is left to the Models
 * that extend this Model.
 * 
 * @var string
 * @access public
 */    
    var $cacheFolder = 'web';
/**
 * construct
 * 
 * @param boolean $id
 * @param string $table
 * @param string $ds 
 * @return void
 * @access private
 */
    function __construct($id = false, $table = null, $ds = null)
    {
		
        parent::__construct($id, $table, $ds);
        
        $this->cacheFolder = str_replace('/', DS, $this->cacheFolder);

        if ($this->cacheFolder=='web' || empty($this->cacheFolder))
            $this->cacheFolder = 'web'.DS;
        
        if (substr($this->cacheFolder, -1, 1)!=DS)
            $this->cacheFolder = $this->cacheFolder.DS;       

    }    
/**
 * httpPost
 * 
 * @param string $url
 * @param string $vars
 * @param string $headers
 * @param string $cookie_file
 * @param string $timeout
 * @return mixed 
 * @access public
 */
    function httpPost($url, $vars = null, $headers = null, $cookie_file = null, $timeout = null)
    {
		
        $vars = $this->__toUrlData($vars);

    	$ch = curl_init();
    	if (! $ch)
    	{			
    		return false;
    	}
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	
    	// Don't check certifications that closley if not required, fixed some issues for me before
    	if ($this->ssl_strict==false)
    	{
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);    	
    	}
    	
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);        	

        if (empty($timeout))
            $timeout = $this->connection_timeout;    
        
        curl_setopt($ch,CURLOPT_TIMEOUT, $timeout);
    	curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); // follow redirects recursively    	
        
        if (!empty($headers))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        
        if (!empty($cookie_file))
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        }
            
    	$response = curl_exec($ch);
    	curl_close($ch);           	
			    
        return $response;
    
    }
/**
 * httpGet
 * 
 * @param string $url
 * @param string $vars
 * @param string $headers
 * @param string $cookie_file
 * @param string $timeout
 * @return string
 * @access string 
 */
    function httpGet($url, $vars = null, $headers = null, $cookie_file = null, $timeout = null)
    {
	
        if (!empty($vars))
            $url = $url.'?'.$this->__toUrlData($vars);


        $ch = curl_init();
        
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    	
    	
    	// Don't check certifications that closley if not required, fixed some issues for me before
    	if ($this->ssl_strict==false)
    	{
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);    	
    	}    	
    	        
        curl_setopt($ch, CURLOPT_URL, $url);
        
        if (empty($timeout))
            $timeout = $this->connection_timeout;    
        
        curl_setopt($ch,CURLOPT_TIMEOUT, $timeout);        
        
        if (!empty($headers))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        
        if (!empty($cookie_file))
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        }
                       
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper('get'));
        curl_setopt($ch, CURLOPT_VERBOSE, 1); ########### debug
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); // follow redirects recursively
    
        $ret = curl_exec($ch);
        
        curl_close($ch);
        
        
        return $ret;
		
    }  
/**
 * cleanUpCacheFolder
 * 
 * @param int $expires
 * @param string $pattern
 * @return array
 * @access public
 */
    function cleanUpCacheFolder($expires = '+2 hours', $pattern = '.*')
    {
		
        uses('Folder');
        $path = TMP.'cache'.DS.$this->cacheFolder;
        
        $folder =& new Folder($path);        
        $cachedFiles = $folder->find();
        
		if (!is_numeric($expires)) {
			$expires = strtotime($expires);
		}        
		
		$errors = array();
        
        foreach ($cachedFiles as $cacheFile)
        {
            if (preg_match('/'.$pattern.'/iUs', $cacheFile))
            {
                $age = time() - @filemtime($path.$cacheFile);            
                $maxAge = $expires - time();            
                                
                if ($age >= $maxAge)
                {
                    // Well R.I.P. dear cache file ; )
                    if (!@unlink($path.$cacheFile))
                    {
                        $errors[] = $cacheFile;
                    }
                }
            }
        }
        
        if (empty($errors))
            return true;
        else 
            return $errors;
		
    }       
/**
 * toUrlData
 * 
 * @param string $arrayData
 * @return array
 * @access private 
 */
    function __toUrlData($arrayData)
    {
		
        $postData = array();
        
        foreach ($arrayData as $key => $val)
        {
            array_push($postData, $key.'='.urlencode($val));
        }
        
        return join('&', $postData);
		
    }            
    
    /**
     * Creates a unique cache file path by combining all parameters given to a unique MD5 hash
     *
     * @param string $ext The extension for the cache file
     * @return string Returns a unique file path
     * @access private 
     */
    function __createCacheHash($ext = '.txt')
    {
		
        $args = func_get_args();        
        array_shift($args);
        
        $hashSource = null;
        
        foreach ($args as $arg)
        {
            $hashSource = $hashSource . serialize($arg);
        }
        
        return md5($hashSource).$ext;
		
    }    
	
}
