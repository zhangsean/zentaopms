<?php
/**
 * ZenTaoPHP的helper类。
 * The helper class file of ZenTaoPHP framework.
 *
 * The author disclaims copyright to this source code. In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */

/**
 * 该类实现了一些常用的方法
 * The helper class, contains the tool functions.
 *
 * @package framework
 */
include dirname(__FILE__) . '/base/helper.class.php';
class helper extends baseHelper
{
    public static function getViewType($source = false)
    {
        global $config, $app;
        if($config->requestType != 'GET')
        {
            $pathInfo = $app->getPathInfo();
            if(!empty($pathInfo))
            {
                $dotPos = strrpos($pathInfo, '.');
                if($dotPos)
                {
                    $viewType = substr($pathInfo, $dotPos + 1);
                }
                else
                {
                    $config->default->view = $config->default->view == 'mhtml' ? 'html' : $config->default->view;
                }
            }
        }
        elseif($config->requestType == 'GET')
        {
            if(isset($_GET[$config->viewVar]))
            {
                $viewType = $_GET[$config->viewVar];
            }
            else
            {
                /* Set default view when url has not module name. such as only domain. */
                $config->default->view = ($config->default->view == 'mhtml' and isset($_GET[$config->moduleVar])) ? 'html' : $config->default->view;
            }
        }
        if($source and isset($viewType)) return $viewType;

        if(isset($viewType) and strpos($config->views, ',' . $viewType . ',') === false) $viewType = $config->default->view;
        return isset($viewType) ? $viewType : $config->default->view;
    }

    /**
     * Encode json for $.parseJSON
     *
     * @param  array  $data
     * @param  int    $options
     * @static
     * @access public
     * @return string
     */
    public static function jsonEncode4Parse($data, $options = 0)
    {
        $json = json_encode($data);
        if($options) $json = str_replace(array("'", '"'), array('\u0027', '\u0022'), $json);

        $escapers     = array("\\",  "/",   "\"", "'", "\n",  "\r",  "\t", "\x08", "\x0c", "\\\\u");
        $replacements = array("\\\\", "\\/", "\\\"", "\'", "\\n", "\\r", "\\t",  "\\f",  "\\b", "\\u");
        return str_replace($escapers, $replacements, $json);
    }

    /**
     * Verify that the system has opened on the feature.
     *
     * @param  string    $feature    scrum_risk | risk | scrum
     * @static
     * @access public
     * @return bool
     */
    public static function hasFeature($feature)
    {
        global $config;

        if(strpos($feature, '_') !== false)
        {
            $code = explode('_', $feature);
            $code = $code[0] . ucfirst($code[1]);
            return strpos(",$config->disabledFeatures,", ",{$code},") === false;
        }
        else
        {
            if($feature == 'product' or $feature == 'scrum' or $feature == 'waterfall') return strpos(",$config->disabledFeatures,", ",{$feature},") === false;

            $hasFeature       = false;
            $canConfigFeature = false;
            foreach($config->featureGroup as $group => $modules)
            {
                foreach($modules as $module)
                {
                    if($feature == $group or $feature == $module)
                    {
                        $canConfigFeature = true;
                        if(helper::hasFeature("{$group}_{$module}")) $hasFeature = true;
                    }
                }
            }
            return !$canConfigFeature or ($hasFeature && strpos(",$config->disabledFeatures,", ",{$feature},") === false);
        }
    }

    /**
     * Convert encoding.
     *
     * @param  string $string
     * @param  string $fromEncoding
     * @param  string $toEncoding
     * @static
     * @access public
     * @return string
     */
    public static function convertEncoding($string, $fromEncoding, $toEncoding = 'utf-8')
    {
        $toEncoding = str_replace('utf8', 'utf-8', $toEncoding);
        if(function_exists('mb_convert_encoding'))
        {
            /* Remove like utf-8//TRANSLIT. */
            $position = strpos($toEncoding, '//');
            if($position !== false) $toEncoding = substr($toEncoding, 0, $position);

            /* Check string encoding. */
            $encodings = array_merge(array('GB2312','GBK','BIG5'), mb_list_encodings());
            $encoding  = strtolower(mb_detect_encoding($string, $encodings));
            if($encoding == $toEncoding) return $string;
            return mb_convert_encoding($string, $toEncoding, $encoding);
        }
        elseif(function_exists('iconv'))
        {
            if($fromEncoding == $toEncoding) return $string;
            $convertString = @iconv($fromEncoding, $toEncoding, $string);
            /* iconv error then return original. */
            if(!$convertString) return $string;
            return $convertString;
        }

        return $string;
    }

    /**
     * Calculate two working days.
     *
     * @param string $begin
     * @param string $end
     *
     * @return bool|float
     */
    public static function workDays($begin, $end)
    {
        $begin = strtotime($begin);
        $end   = strtotime($end);
        if($end < $begin) return false;

        $double = floor(($end - $begin) / (7 * 24 * 3600));
        $begin  = date('w', $begin);
        $end    = date('w', $end);
        $end    = $begin > $end ? $end + 5 : $end;
        return $double * 5 + $end - $begin;
    }

    /**
     * Unify string to standard chars.
     *
     * @param  string    $string
     * @param  string    $to
     * @static
     * @access public
     * @return string
     */
    public static function unify($string, $to = ',')
    {
        $labels = array('_', '、', ' ', '-', '?', '@', '&', '%', '~', '`', '+', '*', '/', '\\', '，', '。');
        $string = str_replace($labels, $to, $string);
        return preg_replace("/[{$to}]+/", $to, trim($string, $to));
    }

    /**
     * Format version to semver formate.
     *
     * @param  string    $version
     * @static
     * @access public
     * @return string
     */
    public static function formatVersion($version)
    {
        return preg_replace_callback(
            '/([0-9]+)((?:\.[0-9]+)?)((?:\.[0-9]+)?)(?:[\s\-\+]?)((?:[a-z]+)?)((?:\.?[0-9]+)?)/i',
            function($matches)
            {
                $major      = $matches[1];
                $minor      = $matches[2];
                $patch      = $matches[3];
                $preRelease = $matches[4];
                $build      = $matches[5];

                $versionStrs = array(
                    $major,
                    $minor ?: ".0",
                    $patch ?: ".0",
                );

                if($preRelease ?: $build) array_push($versionStrs, "-");
                if($preRelease) array_push($versionStrs, $preRelease);
                if($build)
                {
                    if(!$preRelease) array_push($versionStrs, "build");
                    if(mb_substr($build, 0, 1) !== ".") array_push($versionStrs, ".");

                    array_push($versionStrs, $build);
                }
                return join("", $versionStrs);
            },
            $version
        );
    }

	/**
	 * Trim version to xuanxuan version format.
	 *
	 * @param  string    $version
	 * @access public
	 * @return string
	 */
	public function trimVersion($version)
	{
		return preg_replace_callback(
			'/([0-9]+)((?:\.[0-9]+)?)((?:\.[0-9]+)?)(?:[\s\-\+]?)((?:[a-z]+)?)((?:\.?[0-9]+)?)/i',
			function($matches)
			{
				$major      = $matches[1];
				$minor      = $matches[2];
				$patch      = $matches[3];
				$preRelease = $matches[4];
				$build      = $matches[5];

				$versionStrs = array(
					$major,
					$minor ?: ".0",
				);

				if($patch && $patch !== ".0" && $patch !== "0") array_push($versionStrs, $patch);
				if($preRelease ?: $build) array_push($versionStrs, " ");
				if($preRelease) array_push($versionStrs, $preRelease);
				if($build)
				{
					if(!$preRelease) array_push($versionStrs, "build");
					array_push($versionStrs, mb_substr($build, 0, 1) === "." ? substr($build, 1) : $build);
				}
				return join("", $versionStrs);
			},
			$version
		);
	}

    /**
     * Request API.
     *
     * @param  string    $url
     * @static
     * @access public
     * @return string
     */
    static public function requestAPI($url)
    {
        global $config;

        $url .= (strpos($url, '?') !== false ? '&' : '?') . $config->sessionVar . '=' . session_id();
        if(isset($_SESSION['user'])) $url .= '&account=' . $_SESSION['user']->account;
        $response = common::http($url);
        $jsonDecode = json_decode($response);
        if(empty($jsonDecode)) return $response;
        return $jsonDecode;
    }

    /**
     * 代替 die、exit 函数终止并输出
     *
     * @param string $content
     * @return void
     */
    public static function end($content = '')
    {
        throw EndResponseException::create($content);
    }
}

/**
 * 检查是否是onlybody模式。
 * Check exist onlybody param.
 *
 * @access public
 * @return void
 */
function isonlybody()
{
    return helper::inOnlyBodyMode();
}

/**
 * Format time.
 *
 * @param  int    $time
 * @param  string $format
 * @access public
 * @return void
 */
function formatTime($time, $format = '')
{
    $time = str_replace('0000-00-00', '', $time);
    $time = str_replace('00:00:00', '', $time);
    if(trim($time) == '') return '';
    if($format) return date($format, strtotime($time));
    return trim($time);
}

/**
 * Fix for session error.
 *
 * @param  int    $class
 * @access protected
 * @return void
 */
function autoloader($class)
{
    if(!class_exists($class))
    {
        if($class == 'post_max_size' or $class == 'max_input_vars') eval('class ' . $class . ' {};');
    }
}

spl_autoload_register('autoloader');
