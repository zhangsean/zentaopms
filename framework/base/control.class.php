<?php
/**
 * ZenTaoPHP的baseControl类。
 * The baseControl class file of ZenTaoPHP framework.
 *
 * @package framework
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class baseControl
{
    /**
     * 全局对象 $app。
     * The global $app object.
     *
     * @var object
     * @access public
     */
    public $app;

    /**
     * 应用名称 $appName
     * The global $appName.
     *
     * @var string
     * @access public
     */
    public $appName;

    /**
     * 全局对象 $config。
     * The global $config object.
     *
     * @var object
     * @access public
     */
    public $config;

    /**
     * 全局对象 $lang。
     * The global $lang object.
     *
     * @var object
     * @access public
     */
    public $lang;

    /**
     * 全局对象 $dbh，数据库连接句柄。
     * The global $dbh object, the database connection handler.
     *
     * @var object
     * @access public
     */
    public $dbh;

    /**
     * $dao对象，实现sql的拼装和执行。
     * The $dao object, used to join sql and excute sql.
     *
     * @var dao
     * @access public
     */
    public $dao;

    /**
     * $post对象，用户可以通过$this->post->key来引用$_POST变量。
     * The $post object, useer can access a post var by $this->post->key.
     *
     * @var object
     * @access public
     */
    public $post;

    /**
     * $get对象，用户可以通过$this->get->key来引用$_GET变量。
     * The $get object, useer can access a get var by $this->get->key.
     *
     * @var object
     * @access public
     */
    public $get;

    /**
     * $session对象，用户可以通过$this->session->key来引用$_SESSION变量。
     * The $session object, useer can access a session var by $this->session->key.
     *
     * @var object
     * @access public
     */
    public $session;

    /**
     * $server对象，用户可以通过$this->server->key来引用$_SERVER变量。
     * The $server object, useer can access a server var by $this->server->key.
     *
     * @var object
     * @access public
     */
    public $server;

    /**
     * $cookie对象，用户可以通过$this->cookie->key来引用$_COOKIE变量。
     * The $cookie object, useer can access a cookie var by $this->cookie->key.
     *
     * @var object
     * @access public
     */
    public $cookie;

    /**
     * 当前模块的名称。
     * The name of current module.
     *
     * @var string
     * @access public
     */
    public $moduleName;

    /**
     * $view用于存放从control传到view视图的数据。
     * The vars assigned to the view page.
     *
     * @var object
     * @access public
     */
    public $view;

    /**
     * 视图的类型，比如html, json。
     * The type of the view, such html, json.
     *
     * @var string
     * @access public
     */
    public $viewType;

    /**
     * 输出到浏览器的内容。
     * The content to display.
     *
     * @var string
     * @access public
     */
    public $output;

    /**
     * 客户端设备。
     * The client device.
     *
     * @var string
     * @access public
     */
    public $clientDevice;

    /**
     * 不同设备下视图文件的前缀。
     * The prefix of view file for mobile or PC.
     *
     * @var string
     * @access public
     */
    public $devicePrefix;

    /**
     * 构造方法。
     *
     * 1. 将全局变量设为baseControl类的成员变量，方便baseControl的派生类调用；
     * 2. 设置当前模块，读取该模块的model类；
     * 3. 初始化$view视图类。
     *
     * The construct function.
     *
     * 1. global the global vars, refer them by the class member such as $this->app.
     * 2. set the paths of current module, and load it's model class.
     * 3. auto assign the $lang and $config to the view.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @param  string $appName
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '', $appName = '')
    {
        /*
         * 将全局变量设为baseControl类的成员变量，方便baseControl的派生类调用。
         * Global the globals, and refer them as a class member.
         */
        global $app, $config, $lang, $dbh, $common;
        $this->app      = $app;
        $this->config   = $config;
        $this->lang     = $lang;
        $this->dbh      = $dbh;
        $this->viewType = $this->app->getViewType();
        $this->appName  = $appName ? $appName : $this->app->getAppName();

        /**
         * 设置当前模块，读取该模块的model类。
         * Load the model file auto.
         */
        $this->setModuleName($moduleName);
        $this->setMethodName($methodName);
        $this->loadModel($this->moduleName, $appName);

        /**
         * 检查用户是否登录，如果没有登录，跳转到登录页面。
         * Check the user has logon or not, if not, goto the login page.
         */
        if($this->config->installed && !in_array($this->moduleName, $this->config->openModules) && empty($this->app->user) && !$this->loadModel('common')->isOpenMethod($this->moduleName, $this->methodName))
        {
            $uri = $this->app->getURI(true);
            if($this->moduleName == 'message' and $this->methodName == 'ajaxgetmessage')
            {
                $uri = helper::createLink('my');
            }
            elseif(helper::isAjaxRequest())
            {
                die(json_encode(array('result' => false, 'message' => $this->lang->error->loginTimeout)));
            }

            $referer = helper::safe64Encode($uri);
            die(js::locate(helper::createLink('user', 'login', "referer=$referer")));
        }

        /**
         * 如果客户端是手机的话，视图文件增加m.前缀。
         * If the client is mobile, add m. as prefix for view file.
         */
        $this->setClientDevice();
        $this->setDevicePrefix();

        /**
         * 初始化$view视图类。
         * Init the view vars.
         */
        $this->view         = new stdclass();
        $this->view->app    = $app;
        $this->view->lang   = $lang;
        $this->view->config = $config;
        $this->view->common = $common;
        $this->view->title  = '';

        /**
         * 设置超级变量，从$app引用过来。
         * Set super vars.
         */
        $this->setSuperVars();
    }

    //-------------------- Model相关方法(Model related methods) --------------------//

    /*
     * 设置模块名。
     * Set the module name.
     *
     * @param   string  $moduleName  模块名，如果为空，则从$app中获取. The module name, if empty, get it from $app.
     * @access  public
     * @return  void
     */
    public function setModuleName($moduleName = '')
    {
        $this->moduleName = $moduleName ? strtolower($moduleName) : $this->app->getModuleName();
    }

    /**
     * 设置方法名。
     * Set the method name.
     *
     * @param  string $methodName 方法名，如果为空，则从$app中获取。The method name, if empty, get it from $app.
     * @access  public
     * @return  void
     */
    public function setMethodName($methodName = '')
    {
        $this->methodName = $methodName ? strtolower($methodName) : $this->app->getMethodName();
    }

    /**
     * 加载指定模块的model文件。
     * Load the model file of one module.
     *
     * @param  string $moduleName 模块名，如果为空，使用当前模块。The module name, if empty, use current module's name.
     * @param  string $appName    The app name, if empty, use current app's name.
     * @access  public
     * @return  object|bool 如果没有model文件，返回false，否则返回model对象。If no model file, return false, else return the model object.
     */
    public function loadModel($moduleName = '', $appName = '')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        if(empty($appName)) $appName = $this->appName;

        global $loadedModels;
        if(isset($loadedModels[$appName][$moduleName]))
        {
            $this->$moduleName = $loadedModels[$appName][$moduleName];
            $this->dao         = $this->$moduleName->dao;
            return $this->$moduleName;
        }

        $modelFile = $this->app->setModelFile($moduleName, $appName);

        /**
         * 如果没有model文件，尝试加载config配置信息。
         * If no model file, try load config.
         */
        if(!helper::import($modelFile))
        {
            $this->app->loadModuleConfig($moduleName, $appName);
            $this->app->loadLang($moduleName, $appName);
            $this->dao = new dao();
            return false;
        }

        /**
         * 如果没有扩展文件，model类名是$moduleName + 'model'，如果有扩展，还需要增加ext前缀。
         * If no extension file, model class name is $moduleName + 'model', else with 'ext' as the prefix.
         */
        $modelClass = class_exists('ext' . $appName . $moduleName . 'model') ? 'ext' . $appName . $moduleName . 'model' : $appName . $moduleName . 'model';
        if(!class_exists($modelClass))
        {
            $modelClass = class_exists('ext' . $moduleName . 'model') ? 'ext' . $moduleName . 'model' : $moduleName . 'model';
            if(!class_exists($modelClass)) $this->app->triggerError(" The model $modelClass not found", __FILE__, __LINE__, $exit = true);
        }

        /**
         * 初始化model对象，在control对象中可以通过$this->$moduleName来引用。同时将dao对象赋为control对象的成员变量，方便引用。
         * Init the model object thus you can try $this->$moduleName to access it. Also assign the $dao object as a member of control object.
         */
        $loadedModels[$appName][$moduleName] = new $modelClass($appName);
        $this->$moduleName                   = $loadedModels[$appName][$moduleName];
        $this->dao                           = $this->$moduleName->dao;
        return $this->$moduleName;
    }

    /**
     * 设置超级全局变量，方便直接引用。
     * Set the super vars.
     *
     * @access public
     * @return void
     */
    public function setSuperVars()
    {
        $this->post    = $this->app->post;
        $this->get     = $this->app->get;
        $this->server  = $this->app->server;
        $this->session = $this->app->session;
        $this->cookie  = $this->app->cookie;
    }

    /**
     * 设置客户端的设备类型。
     * Set client device.
     *
     * @access public
     * @return void
     */
    public function setClientDevice()
    {
        $this->clientDevice = $this->app->clientDevice;
    }

    /**
     * 如果客户端是手机的话，视图文件增加m.前缀。
     * If the clent is mobile, add m. as prefix for view file.
     *
     * @access public
     * @return void
     */
    public function setDevicePrefix()
    {
        $this->devicePrefix = zget($this->config->devicePrefix, $this->viewType, '');
    }

    //-------------------- 视图相关方法(View related methods) --------------------//

    /**
     * 设置视图文件：主视图文件，扩展视图文件， 站点扩展视图文件，以及钩子脚本。
     * Set view files: the main file, extension view file, site extension view file and hook files.
     *
     * @param  string $moduleName module name
     * @param  string $methodName method name
     * @access public
     * @return string  the view file
     */
    public function setViewFile($moduleName, $methodName)
    {
        $moduleName = strtolower(trim($moduleName));
        $methodName = strtolower(trim($methodName));

        $modulePath  = $this->app->getModulePath($this->appName, $moduleName);
        $viewExtPath = $this->app->getModuleExtPath($this->appName, $moduleName, 'view');

        $viewType     = $this->viewType == 'mhtml' ? 'html' : $this->viewType;
        $mainViewFile = $modulePath . 'view' . DS . $this->devicePrefix . $methodName . '.' . $viewType . '.php';
        $viewFile     = $mainViewFile;

        if(!empty($viewExtPath))
        {
            $commonExtViewFile = $viewExtPath['common'] . $this->devicePrefix . $methodName . ".{$viewType}.php";
            $siteExtViewFile   = empty($viewExtPath['site']) ? '' : $viewExtPath['site'] . $this->devicePrefix . $methodName . ".{$viewType}.php";

            $viewFile = file_exists($commonExtViewFile) ? $commonExtViewFile : $mainViewFile;
            $viewFile = (!empty($siteExtViewFile) and file_exists($siteExtViewFile)) ? $siteExtViewFile : $viewFile;
            if(!is_file($viewFile)) $this->app->triggerError("the view file $viewFile not found", __FILE__, __LINE__, $exit = true);

            $commonExtHookFiles = glob($viewExtPath['common'] . $this->devicePrefix . $methodName . ".*.{$viewType}.hook.php");
            $siteExtHookFiles   = empty($viewExtPath['site']) ? '' : glob($viewExtPath['site'] . $this->devicePrefix . $methodName . ".*.{$viewType}.hook.php");
            $extHookFiles       = array_merge((array)$commonExtHookFiles, (array)$siteExtHookFiles);
        }

        if(!empty($extHookFiles)) return array('viewFile' => $viewFile, 'hookFiles' => $extHookFiles);
        return $viewFile;
    }

    /**
     * 获取某一个视图文件的扩展。
     * Get the extension file of an view.
     *
     * @param  string $viewFile
     * @access public
     * @return string|bool  If extension view file exists, return the path. Else return fasle.
     */
    public function getExtViewFile($viewFile)
    {
        /**
         * 首先找sitecode下的扩展文件，如果没有，再找ext下的扩展文件。
         * Find extViewFile in ext/_$siteCode/view first, then try ext/view/.
         */
        $moduleName = basename(dirname(dirname(realpath($viewFile))));
        $extPath    = $this->app->getModuleExtPath('', $moduleName, 'view');

        $checkedOrder = array('site', 'saas', 'custom', 'vision', 'xuan', 'common');
        $fileName     = basename($viewFile);
        foreach($checkedOrder as $checkedType)
        {
            if(!empty($extPath[$checkedType]))
            {
                $extViewFile = $extPath[$checkedType] . $fileName;
                if(file_exists($extViewFile))
                {
                    helper::cd($extPath[$checkedType]);
                    return $extViewFile;
                }
            }
        }

        return false;
    }

    /**
     * 获取适用于当前方法的css：该模块公用的css + 当前方法的css + 扩展的css。
     * Get css codes applied to current method: module common css + method css + extension css.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return string
     */
    public function getCSS($moduleName, $methodName)
    {
        $moduleName = strtolower(trim($moduleName));
        $methodName = strtolower(trim($methodName));

        $modulePath = $this->app->getModulePath($this->appName, $moduleName);
        $cssExtPath = $this->app->getModuleExtPath($this->appName, $moduleName, 'css');

        $clientLang = $this->app->getClientLang();
        $notCNLang  = strpos('|zh-cn|zh-tw|', "|{$clientLang}|") === false;

        $css          = '';
        $devicePrefix = $this->devicePrefix;
        $mainCssPath  = $modulePath . 'css' . DS;

        /* Common css file. like module/story/css/common.css. */
        $mainCssFile = $mainCssPath . $devicePrefix . 'common.css';
        if(is_file($mainCssFile)) $css .= file_get_contents($mainCssFile);

        /* Common css file with lang. like module/story/css/common.en.css. */
        $mainCssLangFile = $mainCssPath . $devicePrefix . "common.{$clientLang}.css";
        if(!file_exists($mainCssLangFile) and $notCNLang) $mainCssLangFile = $mainCssPath . $devicePrefix . "common.en.css";
        if(is_file($mainCssLangFile)) $css .= file_get_contents($mainCssLangFile);

        /* Method css file. like module/story/css/create.css. */
        $methodCssFile = $mainCssPath . $devicePrefix . $methodName . '.css';
        if(is_file($methodCssFile)) $css .= file_get_contents($methodCssFile);

        /* Method css file with lang. like module/story/css/create.en.css. */
        $methodCssLangFile = $mainCssPath . $devicePrefix . "{$methodName}.{$clientLang}.css";
        if(!file_exists($methodCssLangFile) and $notCNLang) $methodCssLangFile = $mainCssPath . $devicePrefix . "{$methodName}.en.css";
        if(is_file($methodCssLangFile)) $css .= file_get_contents($methodCssLangFile);

        if(!empty($cssExtPath))
        {
            $realModulePath = realPath($modulePath);
            foreach($cssExtPath as $cssPath)
            {
                if(empty($cssPath)) continue;

                $cssMethodExt = $cssPath . $methodName . DS;
                $cssCommonExt = $cssPath . 'common' . DS;

                $cssExtFiles = glob($cssCommonExt . $devicePrefix . '*.css');
                if(!empty($cssExtFiles) and is_array($cssExtFiles)) $css .= $this->getExtCSS($cssExtFiles);

                $cssExtFiles = glob($cssMethodExt . $devicePrefix . '*.css');
                if(!empty($cssExtFiles) and is_array($cssExtFiles)) $css .= $this->getExtCSS($cssExtFiles);
            }
        }

        return $css;
    }

    /**
     * Get extension css and extension css with lang.
     *
     * @param  array $files
     * @access public
     * @return string
     */
    public function getExtCSS($files)
    {
        $clientLang = $this->app->getClientLang();
        $notCNLang  = strpos('|zh-cn|zh-tw|', "|{$clientLang}|") === false;

        $filePairs = array();
        foreach($files as $cssFile)
        {
            $fileName             = basename($cssFile);
            $filePairs[$fileName] = $cssFile;
        }

        $css       = '';
        $usedCodes = array();
        foreach($filePairs as $fileName => $cssFile)
        {
            if(preg_match('/^\w+\.css$/', $fileName))
            {
                /* Method extension css file. like module/story/ext/css/create/effort.css. */
                $css .= file_get_contents($cssFile);
                list($code) = explode('.', $fileName);
            }
            else
            {
                list($code) = explode('.', $fileName);
                if(isset($usedCodes[$code])) continue;
            }


            /* Method extension css file. like module/story/ext/css/create/effort.zh-cn.css. */
            if(isset($filePairs["{$code}.{$clientLang}.css"]))
            {
                $css .= file_get_contents($filePairs["{$code}.{$clientLang}.css"]);
            }
            elseif($notCNLang and isset($filePairs["{$code}.en.css"]))
            {
                $css .= file_get_contents($filePairs["{$code}.en.css"]);
            }
            $usedCodes[$code] = $code;
        }

        return $css;
    }

    /**
     * 获取适用于当前方法的js：该模块公用的js + 当前方法的js + 扩展的js。
     * Get js codes applied to current method: module common js + method js + extension js.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return string
     */
    public function getJS($moduleName, $methodName)
    {
        $moduleName = strtolower(trim($moduleName));
        $methodName = strtolower(trim($methodName));

        $modulePath = $this->app->getModulePath($this->appName, $moduleName);
        $jsExtPath  = $this->app->getModuleExtPath($this->appName, $moduleName, 'js');

        $js           = '';
        $mainJsFile   = $modulePath . 'js' . DS . $this->devicePrefix . 'common.js';
        $methodJsFile = $modulePath . 'js' . DS . $this->devicePrefix . $methodName . '.js';
        if(file_exists($mainJsFile)) $js .= file_get_contents($mainJsFile);
        if(is_file($methodJsFile)) $js .= file_get_contents($methodJsFile);

        if(!empty($jsExtPath))
        {
            $realModulePath = realPath($modulePath);
            foreach($jsExtPath as $jsPath)
            {
                if(empty($jsPath)) continue;

                $jsMethodExt = $jsPath . $methodName . DS;
                $jsCommonExt = $jsPath . 'common' . DS;

                $jsExtFiles = glob($jsCommonExt . $this->devicePrefix . '*.js');
                if(!empty($jsExtFiles) and is_array($jsExtFiles)) foreach($jsExtFiles as $jsFile) $js .= file_get_contents($jsFile);

                $jsExtFiles = glob($jsMethodExt . $this->devicePrefix . '*.js');
                if(!empty($jsExtFiles) and is_array($jsExtFiles)) foreach($jsExtFiles as $jsFile) $js .= file_get_contents($jsFile);
            }
        }

        return $js;
    }

    /**
     * 向$view传递一个变量。
     * Assign one var to the view vars.
     *
     * @param  string $name  the name.
     * @param  mixed  $value the value.
     * @access  public
     * @return  void
     */
    public function assign($name, $value)
    {
        $this->view->$name = $value;
    }

    /**
     * 清空$output。
     * Clear the output.
     *
     * @access public
     * @return void
     */
    public function clear()
    {
        $this->output = '';
    }

    /**
     * 渲染视图文件。
     * Parse view file.
     *
     * @param  string $moduleName module name, if empty, use current module.
     * @param  string $methodName method name, if empty, use current method.
     * @access public
     * @return string the parsed result.
     */
    public function parse($moduleName = '', $methodName = '')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        if(empty($methodName)) $methodName = $this->methodName;

        if($this->viewType == 'json') $this->parseJSON($moduleName, $methodName);
        if($this->viewType != 'json') $this->parseDefault($moduleName, $methodName);

        return $this->output;
    }

    /**
     * 渲染json格式。
     * Parse json format.
     *
     * @param  string $moduleName module name
     * @param  string $methodName method name
     * @access public
     * @return void
     */
    public function parseJSON($moduleName, $methodName)
    {
        unset($this->view->app);
        unset($this->view->config);
        unset($this->view->lang);
        unset($this->view->header);
        unset($this->view->position);
        unset($this->view->moduleTree);
        unset($this->view->common);
        unset($this->view->pager->app);
        unset($this->view->pager->lang);

        $output['status'] = is_object($this->view) ? 'success' : 'fail';
        $output['data']   = json_encode($this->view);
        $output['md5']    = md5(json_encode($this->view));

        $this->output = json_encode($output);
    }

    /**
     * 默认渲染方法，适用于viewType = html的时候。
     * Default parse method when viewType != json, like html.
     *
     * @param  string $moduleName module name
     * @param  string $methodName method name
     * @access public
     * @return void
     */
    public function parseDefault($moduleName, $methodName)
    {
        /**
         * 设置视图文件。(PHP7有一个bug，不能直接$viewFile = $this->setViewFile())。
         * Set viewFile. (Can't assign $viewFile = $this->setViewFile() directly because one php7's bug.)
         */
        $results = $this->setViewFile($moduleName, $methodName);

        $viewFile = $results;
        if(is_array($results)) extract($results);

        /**
         * 获得当前页面的CSS和JS。
         * Get css and js codes for current method.
         */
        $css = $this->getCSS($moduleName, $methodName);
        $js  = $this->getJS($moduleName, $methodName);
        if($css) $this->view->pageCSS = $css;
        if($js) $this->view->pageJS = $js;

        /**
         * 切换到视图文件所在的目录，以保证视图文件里面的include语句能够正常运行。
         * Change the dir to the view file to keep the relative paths work.
         */
        $currentPWD = getcwd();
        chdir(dirname($viewFile));

        /**
         * 使用extract安定ob方法渲染$viewFile里面的代码。
         * Use extract and ob functions to eval the codes in $viewFile.
         */
        extract((array)$this->view);
        ob_start();
        include $viewFile;
        if(isset($hookFiles)) foreach($hookFiles as $hookFile) if(file_exists($hookFile)) include $hookFile;
        $this->output .= ob_get_contents();
        ob_end_clean();

        /**
         * 渲染完毕后，再切换回之前的路径。
         * At the end, chang the dir to the previous.
         */
        chdir($currentPWD);
    }

    /**
     * 获取一个方法的输出内容，这样我们可以在一个方法里获取其他模块方法的内容。
     * 如果模块名为空，则调用该模块、该方法；如果设置了模块名，调用指定模块指定方法。
     *
     * Get the output of one module's one method as a string, thus in one module's method, can fetch other module's content.
     * If the module name is empty, then use the current module and method. If set, use the user defined module and method.
     *
     * @param  string $moduleName module name.
     * @param  string $methodName method name.
     * @param  array  $params     params.
     * @access  public
     * @return  string  the parsed html.
     */
    public function fetch($moduleName = '', $methodName = '', $params = array(), $appName = '')
    {
        /**
         * 如果模块名为空，则调用该模块、该方法。
         * If the module name is empty, then use the current module and method.
         */
        if($moduleName == '') $moduleName = $this->moduleName;
        if($methodName == '') $methodName = $this->methodName;
        if($appName == '') $appName = $this->appName;
        if($moduleName == $this->moduleName and $methodName == $this->methodName)
        {
            $this->parse($moduleName, $methodName);
            return $this->output;
        }

        $currentModuleName = $this->moduleName;
        $currentMethodName = $this->methodName;
        $currentAppName    = $this->appName;
        $currentParams     = $this->app->getParams();

        /**
         * 设置调用指定模块的指定方法。
         * chang the dir to the previous.
         */
        $this->app->setModuleName($moduleName);
        $this->app->setMethodName($methodName);
        $this->app->setControlFile();

        if(!is_array($params)) parse_str($params, $params);
        $this->app->params = $params;

        $currentPWD = getcwd();

        /**
         * 设置引用的文件和路径。
         * Set the paths and files to included.
         */
        $modulePath        = $this->app->getModulePath($appName, $moduleName);
        $moduleControlFile = $modulePath . 'control.php';
        $actionExtPath     = $this->app->getModuleExtPath($appName, $moduleName, 'control');
        $file2Included     = $moduleControlFile;
        $classNameToFetch  = $moduleName;

        if(!empty($actionExtPath))
        {
            /**
             * 设置公共扩展。
             * set common extension.
             */
            $file2Included = $moduleControlFile;

            if(!empty($actionExtPath['common']))
            {
                $commonActionExtFile = $actionExtPath['common'] . strtolower($methodName) . '.php';
                if(file_exists($commonActionExtFile)) $file2Included = $commonActionExtFile;
            }

            if(!empty($actionExtPath['xuan']))
            {
                $commonActionExtFile = $actionExtPath['xuan'] . strtolower($methodName) . '.php';
                if(file_exists($commonActionExtFile)) $file2Included = $commonActionExtFile;
            }

            if(!empty($actionExtPath['vision']))
            {
                $commonActionExtFile = $actionExtPath['vision'] . strtolower($methodName) . '.php';
                if(file_exists($commonActionExtFile)) $file2Included = $commonActionExtFile;
            }

            $commonActionExtFile = $actionExtPath['custom'] . strtolower($methodName) . '.php';
            if(file_exists($commonActionExtFile)) $file2Included = $commonActionExtFile;

            if(!empty($actionExtPath['saas']))
            {
                $commonActionExtFile = $actionExtPath['saas'] . strtolower($methodName) . '.php';
                if(file_exists($commonActionExtFile)) $file2Included = $commonActionExtFile;
            }

            if(!empty($actionExtPath['site']))
            {
                /**
                 * 设置站点扩展。
                 * every site has it's extension.
                 */
                $siteActionExtFile = $actionExtPath['site'] . strtolower($methodName) . '.php';
                $file2Included     = file_exists($siteActionExtFile) ? $siteActionExtFile : $file2Included;
            }

            /* If class name is my{$moduleName} then set classNameToFetch for include this file. */
            if(strpos($file2Included, DS . 'ext' . DS) !== false and stripos(file_get_contents($file2Included), "class my{$moduleName} extends $moduleName") !== false) $classNameToFetch = "my{$moduleName}";
        }

        /**
         * 加载控制器文件。
         * Load the control file.
         */
        if(!is_file($file2Included)) $this->app->triggerError("The control file $file2Included not found", __FILE__, __LINE__, $exit = true);
        if(!class_exists($classNameToFetch))
        {
            chdir(dirname($file2Included));
            helper::import($file2Included);
        }

        /**
         * 设置调用的类名。
         * Set the name of the class to be called.
         */
        $className = class_exists("my$moduleName") ? "my$moduleName" : $moduleName;
        if(!class_exists($className)) $this->app->triggerError(" The class $className not found", __FILE__, __LINE__, $exit = true);

        /**
         * 解析参数，创建模块control对象。
         * Parse the params, create the $module control object.
         */
        $module           = new $className($moduleName, $methodName, $appName);
        $module->viewType = $this->viewType;

        /**
         * 调用对应方法，使用ob方法获取输出内容。
         * Call the method and use ob function to get the output.
         */
        ob_start();
        call_user_func_array(array($module, $methodName), array_values($params));
        $output = ob_get_contents();
        ob_end_clean();

        unset($module);

        /**
         * 切换回之前的模块和方法。
         * Chang the module、method to the previous.
         */
        $this->app->setModuleName($currentModuleName);
        $this->app->setMethodName($currentMethodName);
        $this->app->params = $currentParams;

        chdir($currentPWD);

        /**
         * 返回内容。
         * Return the content.
         */
        return $output;
    }

    /**
     * 向浏览器输出内容。
     * Print the content of the view.
     *
     * @param  string $moduleName module name
     * @param  string $methodName method name
     * @access  public
     * @return  void
     */
    public function display($moduleName = '', $methodName = '')
    {
        if(empty($this->output)) $this->parse($moduleName, $methodName);
        echo $this->output;
    }

    /**
     * 直接输出data数据，通常用于ajax请求中。
     * Send data directly, for ajax requests.
     *
     * @param  mixed  $data
     * @param  string $type
     * @access public
     * @return void
     */
    public function send($data, $type = 'json')
    {
        if($type != 'json') die();

        $data = (array)$data;

        /* Make sure locate in this tab. */
        global $lang;
        $moduleName = $this->app->rawModule;
        if(isset($lang->navGroup->{$moduleName}) and $lang->navGroup->{$moduleName} != $this->app->tab and isset($data['locate']) and $data['locate'][0] == '/' and !helper::inOnlyBodyMode()) $data['locate'] .= "#app={$this->app->tab}";

        if(helper::isAjaxRequest() or $this->viewType == 'json')
        {
            /* Process for zh-cn in json. */
            foreach($data as $key => $value)
            {
                if(!is_string($value)) continue;

                /* Retain ["] for json encode when value is jsoned string. */
                $data[$key] = str_replace('%22', '"', urlencode($value));
            }

            if(defined('RUN_MODE') and in_array(RUN_MODE, array('api', 'xuanxuan')))
            {
                print(urldecode(json_encode($data)));
                $response = helper::removeUTF8Bom(ob_get_clean());
                return print($response);
            }

            $obLevel = ob_get_level();
            for($i = 0; $i < $obLevel; $i++) ob_end_clean();

            $response = helper::removeUTF8Bom(urldecode(json_encode($data)));
            die($response);
        }

        /**
         * 响应非ajax的请求。
         * Response request not ajax.
         */
        if(isset($data['result']) and $data['result'] == 'success')
        {
            if(!empty($data['message'])) echo js::alert($data['message']);
            $locate = isset($data['locate']) ? $data['locate'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
            if(!empty($locate)) die(js::locate($locate));
            die(isset($data['message']) ? $data['message'] : 'success');
        }

        if(isset($data['result']) and $data['result'] == 'fail')
        {
            if(!empty($data['message']))
            {
                if(is_string($data['message']))
                {
                    echo js::alert($data['message']);
                    $locate = isset($data['locate']) ? $data['locate'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
                    if (!empty($locate)) die(js::locate($locate));
                    die(isset($data['message']) ? $data['message'] : 'fail');
                }

                $message = json_decode(json_encode($data['message']), true);
                foreach($message as $item => $errors) $message[$item] = implode(',', $errors);
                die(js::alert(strip_tags(implode('\n', $message))));
            }
            die('fail');
        }
    }

    /**
     * return error json
     *
     * @param  mixed $error
     * @return void
     * @author thanatos thanatos915@163.com
     */
    public function sendError($error)
    {
        $this->send(array('result' => 'fail', 'message' => $error));
    }

    /**
     * send success json
     *
     * @param  array $data
     * @return void
     * @author thanatos thanatos915@163.com
     */
    public function sendSuccess($data)
    {
        $data['result'] = 'success';
        if(empty($data['message'])) $data['message'] = $this->lang->saveSuccess;
        $this->send($data);
    }

    /**
     * 创建一个模块方法的链接。
     * Create a link to one method of one module.
     *
     * @param  string       $moduleName module name
     * @param  string       $methodName method name
     * @param  string|array $vars       the params passed, can be array(key=>value) or key1=value1&key2=value2
     * @param  string       $viewType   the view type
     * @param  string       $onlybody   remove header and footer or not in iframe
     * @access  public
     * @return  string the link string.
     */
    public function createLink($moduleName, $methodName = 'index', $vars = array(), $viewType = '', $onlybody = false)
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        return helper::createLink($moduleName, $methodName, $vars, $viewType, $onlybody);
    }

    /**
     * 创建当前模块的一个方法链接。
     * Create a link to the inner method of current module.
     *
     * @param  string       $methodName method name
     * @param  string|array $vars       the params passed, can be array(key=>value) or key1=value1&key2=value2
     * @param  string       $viewType   the view type
     * @access  public
     * @return  string  the link string.
     */
    public function inlink($methodName = 'index', $vars = array(), $viewType = '', $onlybody = false)
    {
        return helper::createLink($this->moduleName, $methodName, $vars, $viewType, $onlybody);
    }

    /**
     * 重定向到另一个页面。
     * Location to another page.
     *
     * @param  string $url the target url.
     * @access  public
     * @return  void
     */
    public function locate($url)
    {
        header("location: $url");
        exit;
    }
}
