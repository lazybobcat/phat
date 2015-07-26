<?php

namespace Phat\View;

use Phat\Core\Configure;
use Phat\Core\Exception\LogicErrorException;
use Phat\Http\Request;
use Phat\View\Exception\MissingTemplateException;

class View
{
    // TODO : inheritance with extends()
    // TODO : element()
    // TODO : Helpers

    /**
     * @var Request The current request
     */
    protected $request;

    /**
     * @var string The Controller name
     */
    protected $controllerName;

    /**
     * @var array Variables to send to the view
     */
    protected $viewVars = [];

    /**
     * @var bool Has the view already been rendered
     */
    protected $hasRendered = false;

    /**
     * @var ViewBlock Associated ViewBlock container
     */
    protected $blocks;

    /**
     * @var string The layout template name to use to render the view.
     */
    public $layout = 'default';
    /**
     * @var string The view template name to render.
     */
    public $view = null;

    /**
     * The extensions of template files.
     */
    const VIEW_EXTENSION = 'php';

    public function __construct(Request $request, $controllerName, $viewVars = [])
    {
        $action = ($request->prefix ? $request->prefix.'_' : '').$request->action;

        $this->request = $request;
        $this->controllerName = $controllerName;
        $this->view = ($this->view ? $this->view : $action);
        $this->viewVars = $viewVars;
        $this->blocks = new ViewBlock();
    }

    /**
     * Sends a custom variable to the view template.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->viewVars += $key;
        } else {
            $this->viewVars[$key] = $value;
        }
    }

    /**
     * Renders a view with a layout sends back the content to the Controller
     * If not specified, the fetched view will correspond to the current controller action.
     * If not specified, the fetched layout will be the default layout;.
     *
     * @param string|null $view
     * @param string|null $layout
     *
     * @return string
     *
     * @throws MissingTemplateException
     * @throws LogicErrorException
     */
    public function render($view = null, $layout = null)
    {
        if ($this->hasRendered) {
            return;
        }

        $viewFile = $this->getViewFileName($view);
        $layoutFile = $this->getLayoutFileName($layout);

        // Make sure $this is set in the view to this view
        $this->set('this', $this);

        $initialBlocks = count($this->blocks->unclosed());
        $this->blocks->set('content', $this->renderView($viewFile));
        $this->blocks->set('content', $this->renderLayout($layoutFile));
        $remainingBlocks = count($this->blocks->unclosed());

        if ($initialBlocks !== $remainingBlocks) {
            throw new LogicErrorException(sprintf("The block '%s' stayed open.", $this->blocks->active()));
        }

        $this->hasRendered = true;

        return $this->blocks->get('content');
    }

    /**
     * Fetches a view block and returns its content.
     * A view block is a reusable part of a View and can be used to control where a part o the view will be display, whether it is in the view or the layout.
     * IE: You can define a 'js' block with View::start('js') and View::end() and then display it in the layout with View::fetch('js').
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function fetch($name, $default = '')
    {
        return $this->blocks->get($name, $default);
    }

    /**
     * Starts a block of content. Will not display the content between View::start() and View::end(), but will buffer it to be displayed
     * later with View::fetch().
     *
     * @param string $name
     *
     * @throws \Phat\Core\Exception\LogicErrorException
     */
    public function start($name)
    {
        $this->blocks->start($name);
    }

    /**
     * Ends a block of content and make it ready to be fetched with View::fetch().
     *
     * @throws \Phat\Core\Exception\LogicErrorException
     */
    public function end()
    {
        $this->blocks->end();
    }

    /**
     * Sets the content of a block. Will override the content if previously set.
     *
     * @param string $name
     * @param string $value
     */
    public function assign($name, $value)
    {
        $this->blocks->set($name, $value);
    }

    public function append($name, $value = null)
    {
        $this->blocks->concat($name, $value, ViewBlock::APPEND);
    }

    public function prepend($name, $value = null)
    {
        $this->blocks->concat($name, $value, ViewBlock::PREPEND);
    }

    /**
     * Renders the passed file and returns back its content
     * This method does not checks if the files exists, make sure it does before calling renderView().
     *
     * @param string $viewFile
     *
     * @return string
     */
    protected function renderView($viewFile)
    {
        extract($this->viewVars);
        ob_start();
        include $viewFile;

        return ob_get_clean();
    }

    /**
     * @see renderView()
     *
     * @param string $layoutFile
     *
     * @return string
     */
    protected function renderLayout($layoutFile)
    {
        return $this->renderView($layoutFile);
    }

    /**
     * Construct the view file path given the view file name and the current app configuration.
     *
     * @param string|null $view
     *
     * @return string
     *
     * @throws MissingTemplateException
     */
    protected function getViewFileName($view = null)
    {
        $config = Configure::read('App');
        $path = '';

        if (!empty($this->request->plugin)) {
            $path .= $config['pluginsDir'].DIRECTORY_SEPARATOR.$this->request->plugin.DIRECTORY_SEPARATOR;
        } else {
            $path .= $config['appDir'].DIRECTORY_SEPARATOR;
        }

        $path .= $config['viewDir'].DIRECTORY_SEPARATOR;
        $path .= $this->controllerName.DIRECTORY_SEPARATOR;

        if (!empty($view)) {
            $path .= $view;
        } else {
            $path .= $this->view;
        }

        $path .= '.'.self::VIEW_EXTENSION;

        if (file_exists($path)) {
            return $path;
        }

        throw new MissingTemplateException("The template file '$path' does not exist.");
    }

    /**
     * Construct the layout file path given the view file name and the current app configuration.
     *
     * @param string|null $layout
     *
     * @return string
     *
     * @throws MissingTemplateException
     */
    protected function getLayoutFileName($layout = null)
    {
        $config = Configure::read('App');
        $path = '';

        if (!empty($this->request->plugin)) {
            $path .= $config['pluginsDir'].DIRECTORY_SEPARATOR.$this->request->plugin.DIRECTORY_SEPARATOR;
        } else {
            $path .= $config['appDir'].DIRECTORY_SEPARATOR;
        }

        $path .= $config['viewDir'].DIRECTORY_SEPARATOR;

        if (!empty($layout)) {
            $path .= $layout;
        } else {
            $path .= $this->layout;
        }

        $path .= '.'.self::VIEW_EXTENSION;

        if (file_exists($path)) {
            return $path;
        }

        throw new MissingTemplateException("The template file '$path' does not exist.");
    }
}
