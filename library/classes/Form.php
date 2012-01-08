<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <email>
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * <class name>
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Form
{
    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Open the form
     *
     * @access  public
     * @param   string  $action
     * @param   array   $options
     * @return  string
     */
    public static function open($action = null, array $options = array())
    {
        $options['action'] = $action;

        if( ! isset($options['method']))
        {
            $options['method'] = 'post';
        }

        if(isset($options['type']))
        {
            switch($options['type'])
            {
                case 'file':
                    $options['enctype'] = 'multipart/form-data';
                    break;
                default:
                    $options['enctype'] = 'application/x-www-form-urlencoded';
                    break;
            }
            unset($options['type']);
        }

        $attributes = static::getAttrStr($options);

        return sprintf('<form %s>' . PHP_EOL, $attributes);
    }

    /**
     * Create an input field
     *
     * @access  public
     * @param   string  $name
     * @param   array   $options
     * @return  string
     */
    public static function input($name = null, array $options = array())
    {
        $options['name'] = $name;

        if( ! isset($options['type']))
        {
            $options['type'] = 'text';
        }

        if($options['name'] === null)
        {
            $options['name'] = $options['type'];
        }

        $label = null;

        if(isset($options['label']))
        {
            $label = static::label($options['name'], $options['label']);
            unset($options['label']);
        }

        $attributes = static::getAttrStr($options);

        return sprintf('%s<input %s/>' . PHP_EOL, $label, $attributes);
    }

    /**
     * Create an input field with the type 'submit'
     *
     * @access  public
     * @param   string  $name
     * @param   array   $options
     * @return  string
     */
    public static function submit($name = null, array $options = array())
    {
        $options['type'] = 'submit';
        return static::input($name, $options);
    }

    /**
     * Create an input field with the type 'password'
     *
     * @access  public
     * @param   string  $name
     * @param   array   $options
     * @return  string
     */
    public static function password($name = null, array $options = array())
    {
        $options['type'] = 'password';
        return static::input($name, $options);
    }

    /**
     * Create an input field with the type 'radio'
     *
     * @access  public
     * @param   string  $name
     * @param   array   $options
     * @return  string
     */
    public static function radio($name = null, array $options = array())
    {
        $options['type'] = 'radio';
        return static::input($name, $options);
    }

    /**
     * Create an input field with the type 'checkbox'
     *
     * @access  public
     * @param   string  $name
     * @param   array   $options
     * @return  string
     */
    public static function checkbox($name = null, array $options = array())
    {
        $options['type'] = 'checkbox';
        return static::input($name, $options);
    }

    /**
     * Create an input field with the type 'reset'
     *
     * @access  public
     * @param   string  $name
     * @param   array   $options
     * @return  string
     */
    public static function reset($name = null, array $options = array())
    {
        $options['type'] = 'reset';
        return static::input($name, $options);
    }

    /**
     * Create an input field with the type 'hidden'
     *
     * @access  public
     * @param   string  $name
     * @param   array   $options
     * @return  string
     */
    public static function hidden($name = null, array $options = array())
    {
        $options['type'] = 'hidden';
        return static::input($name, $options);
    }

    /**
     * Create a button
     *
     * @access  public
     * @param   string  $name
     * @param   string  $text
     * @param   array   $options
     * @return  string
     */
    public static function button($name = null, $text = null, array $options = array())
    {
        $options['name'] = $name;

        if( ! isset($options['type']))
        {
            $options['type'] = 'submit';
        }

        if($options['name'] === null)
        {
            $options['name'] = $options['type'];
        }

        if($text === null)
        {
            $text = $options['name'];
        }
        
        $label = null;

        if(isset($options['label']))
        {
            $label = static::label($options['name'], $options['label']);
            unset($options['label']);
        }

        $attributes = static::getAttrStr($options);

        return sprintf('%s<button %s>%s</button>' . PHP_EOL, $label, $attributes, $text);
    }

    /**
     * Create a textarea
     *
     * @access  public
     * @param   string  $name
     * @param   string  $text
     * @param   array   $options
     * @return  string
     */
    public static function textarea($name = null, $text = null, array $options = array())
    {
        $options['name'] = $name;

        if($options['name'] === null)
        {
            $options['name'] = 'textarea';
        }

        if( ! isset($options['rows']))
        {
            $options['rows'] = 8;
        }
        if( ! isset($options['cols']))
        {
            $options['cols'] = 25;
        }

        $label = null;

        if(isset($options['label']))
        {
            $label = static::label($options['name'], $options['label']);
            unset($options['label']);
        }

        $attributes = static::getAttrStr($options);

        return sprintf('%s<textarea %s>%s</textarea>' . PHP_EOL,
            $label, $attributes, $text);
    }

    /**
     * Create a dropdown list
     *
     * @access  public
     * @param   string  $name
     * @param   array   $items
     * @param   array   $options
     * @return  string
     */
    public static function select($name = null, array $items = array(), array $options = array())
    {
        $options['name'] = $name;

        if($options['name'] === null)
        {
            $options['name'] = 'select';
        }

        $selectList = null;

        foreach($items as $key => $value)
        {
            if(is_array($value))
            {
                $selectList .= sprintf('<optgroup label="%s">' . PHP_EOL, $key);

                foreach($value as $k => $v)
                {
                    $selectList .= sprintf('<option value="%s">%s</option>' . PHP_EOL,
                        $k, $v);
                }

                $selectList .= '</optgroup>' . PHP_EOL;
            }
            else
            {
                $selectList .= sprintf('<option value="%s">%s</option>' . PHP_EOL,
                    $key, $value);
            }
        }

        $label = null;

        if(isset($options['label']))
        {
            $label = static::label($options['name'], $options['label']);
            unset($options['label']);
        }
    
        $attributes = static::getAttrStr($options);

        return sprintf('%s<select %s>' . PHP_EOL . '%s</select>' . PHP_EOL,
            $label, $attributes, $selectList);
    }

    /**
     * Create a label
     *
     * @access  public
     * @param   string  $for
     * @param   string  $text
     * @param   array   $options
     * @return  string
     */
    public static function label($for = null, $text = null, array $options = array())
    {
        $options['for'] = $for;

        if($text === null)
        {
            $text = $options['for'];
        }

        $attributes = static::getAttrStr($options);

        return sprintf('<label %s>%s</label>' . PHP_EOL, $attributes, $text);
    }

    /**
     * End a form, with the possibility for a button field
     *
     * @access  public
     * @param   string  $name
     * @param   string  $text
     * @param   array   $options
     * @return  string
     */
    public static function close($name = null, $text = null, array $options = array())
    {
        if(func_num_args() == 0)
        {
            return '</form>' . PHP_EOL;
        }

        $options['type'] = 'submit';
        return sprintf('%s</form>' . PHP_EOL, static::button($name, $text, $options));
    }

    /**
     * Take an array of attributes and return it as a html attribute-value string
     *
     * @access  protected
     * @param   array   $attributes
     * @return  string
     */
    protected static function getAttrStr(array $attributes)
    {
        $string = null;

        foreach($attributes as $key => $value)
        {
            $string .= sprintf('%s="%s" ', $key, $value);
        }

        return $string;
    }
}
