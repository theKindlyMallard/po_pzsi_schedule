<?php
/**
 * Main application class.
 * 
 * @author theKindlyMallard <the.kindly.mallard@gmail.com>
 */
class Application {
    
    /**
     * @var string The method in controller often named as action.
     */
    private $action = null;
    /**
     * @var string The controller name.
     */
    private $controller = null;
    /** 
     * @var array Contains parameters from URL.
     */
    private $parameters = [];
    
    /**
     * Analyze the URL elements and calls the according controller/method or the fallback.
     * 
     * @author theKindlyMallard <the.kindly.mallard@gmail.com>
     */
    public function __construct() {
        //Get data from url.
        $this->splitUrl();
        //Check if such controller exist.
        if (file_exists(DIR_CONTROLLER . $this->controller . '.php')) {
            //Load this file.
            require_once DIR_CONTROLLER . $this->controller . '.php';
            //Workaround for dynamicly create object from string.
            $controllerName = $this->controller;
            //Create this controller object.
            $this->controller = new $controllerName();
            //Check for method: does such a method exist in the controller?
            if (method_exists($this->controller, $this->action)) {
                //Call the method and pass the arguments to it.
                $this->controller->{$this->action}($this->parameters);
            } else {
                //Default fallback: call the index() method of a selected controller.
                $this->controller->actionIndex();
            }
        } else {
            //Invalid URL, so simply show home/index
            require DIR_CONTROLLER . 'home.php';
            $home = new Home();
            $home->actionIndex();
        }
    }
    
    /**
     * Gets url from $_GET and split it to the parts.
     * 
     * @author theKindlyMallard <the.kindly.mallard@gmail.com>
     */
    private function splitUrl() {
        //Get url from $_URL.
        $url = filter_input(INPUT_GET, 'url');
        
        if (isset($url)) {
            //Split URL.
            $url = rtrim($url, '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            //Put URL parts into according properties.
            $this->controller = isset($url[0]) ? $url[0] : null;
            $this->action = isset($url[1]) ? $url[1] : null;
            //Clear previous parameters.
            $this->parameters = [];
            $urlCount = count($url);
            //Check if sent parameters for action.
            if ($urlCount > 1) {
                for ($i = 2; $i < $urlCount; $i++) {
                    $this->parameters[] = $url[$i];
                }
            }
        }
    }
}
