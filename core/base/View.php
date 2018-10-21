<?php

namespace core\base;


use app\configuration\MainConfigurator;

class View{

    private $path;
    private $template_path = NULL;
    private $params=[];
    private $loader;
    private $twig;

    public function __construct(string $name){
        $this->path = "views/".$name.".twig";
        $this->loader = new \Twig_Loader_Filesystem(APP_PATH);
        $options = MainConfigurator::TEMPLATE_CACHE ? ['cache' => DOCROOT.'cache/views'] : [];
        $this->twig = new \Twig_Environment($this->loader, $options);
    }

    public function __set($name, $value){
        $this->params[$name] = $value;
    }

    private function _render(){
        return $this->twig->render($this->path, $this->params);
    }

    private function renderTemplate(){
        $this->params['content'] = $this->_render();
        return $this->twig->render($this->template_path, $this->params);
    }

    public function render(){
        return $this->template_path === null ? $this->_render() : $this->renderTemplate();
    }

    public function setTemplate($name="default"){
        $this->template_path = "templates/".$name.".twig";
    }

}