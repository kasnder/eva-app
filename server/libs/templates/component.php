<?php
defined('DIR') or die;

/**
 * Component template
 *
 * Provides the scheme for all the inheriting classes that are loaded by the application.
 *
 * @package    Ops
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 */
abstract class ComponentTemplate {
	/**
	 * Stores the default view. Fallback at runtime.
	 * 
	 * @access protected
	 * @var string
	 */
	protected static $_defaultView;

	/**
	 * Flag to force a Ssl connection
	 *
	 * @access protected
	 * @var boolean
	 */
	protected static $_requiresSsl;
	
	/**
	 * Store the calculated view
	 * 
	 * @var string
	 */
	public $view;
	
	/**
	 * Initialise component
	 * 
	 * Set up db connections + determine view.
	 * 
	 * @param string $view
	 * @return void
	 */
	public function __construct($view) {
		// Check if the connection is secure
		if (!Config::DISABLE_SSL && static::$_requiresSsl && $_SERVER["SERVER_PORT"] != 443){
			// Force secure connection
			Common::redirect($_SERVER['REQUEST_URI'], true, true);
		}
		
		// Think a bit about what to wear
		if (empty($view)) {
			$this->view = $this->getDefaultView();
		} else {
			$this->view = $view;
		}
	}
	
	/**
	 * Get the default view.
	 * 
	 * Can be overridden by a component to add conditions.
	 * 
	 * @return string
	 */	
	public function getDefaultView() {
		return static::$_defaultView;
	}
	
	/**
	 * Fire the application.
	 * 
	 * Must be overridden by a component to provide functionality.
	 * 
	 * @return mixed
	 */
	abstract public function launch();
	
	/**
	 * Stores the rendered output
	 * @var mixed
	 * @todo remove
	 */
	public $data;
	
	/**
	 * Stores the results in an array
	 * @var array
	 * @todo implement
	 */
	public $_;
	
	/**
	 * Render the output
	 * @return void
	 */
	public function render($data) {		
		// Load view
		// Get directory of instantiating class
		$reflection = new ReflectionClass($this);
		$file = $reflection->getFileName();
		$dir = dirname($file);
		
		if (!is_file($dir.'/views/'.$this->view.'.php')) {
			$this->view = $this->getDefaultView();
		}
		
		// Render view
		// TODO Rethink concept of a view class
		ob_start();
		
		$type = 'html';
		require($dir.'/views/'.$this->view.'.php');
		$content = ob_get_contents();
		
		ob_end_clean();
		
		// Output view
		switch ($type) {
			case 'json':
				// JSON
				header('Cache-Control: no-cache, must-revalidate');
				header("Content-type:application/json; charset=utf8");

				// GZIP output
				ob_start("ob_gzhandler");
				break;			
			default:
				// HTML
				header("Content-Type: text/html; charset=utf-8");
				break;
		}
		
		echo $content;
		exit;
	}
}
