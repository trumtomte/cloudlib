<?php
/**
 * CloudLib :: Lightweight MVC PHP Framework
 *
 * @author      Sebastian Book <sebbebook@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * The form class.
 *
 * <short description>
 *
 * note: instead of having array of attributes for each type,
 *       have <attribute>->array(<allowed types>)
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.helpers
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Form extends Factory
{
    /**
     * Allowed form and tag attributes
     *
     * @access  private
     * @var     array
     */
    private $attributes = array(
        'form' => array(
            'action' => null,
            'method' => array(
                'get',
                'post'
            ),
            'enctype' => array(
                'multipart/form-data',
                'application/x-www-form-urlencoded'
            ),
            'name'  => null,
            'id'    => null,
            'class' => null,
            'type'  => array(
                'file',
                'get'
            ),
        ),
        'input' => array(
            'type' => array(
                'text',
                'password',
                'checkbox',
                'radio',
                'submit',
                'reset',
                'file',
                'hidden',
                'button'
            ),
            'name'     => null,
            'value'    => null,
            'checked'  => array('checked'),
            'disabled' => array('disabled'),
            'readonly' => array('readonly'),
            'id'       => null,
            'class'    => null
        ),
        'button' => array(
            'name'  => null,
            'value' => null,
            'type'  => array(
                'button',
                'submit',
                'reset'
            ),
            'disabled' => array('disabled'),
            'id'       => null,
            'class'    => null
        ),
        'textarea' => array(
            'name'     => null,
            'rows'     => null,
            'cols'     => null,
            'disabled' => array('disabled'),
            'readonly' => array('readonly'),
            'id'       => null,
            'class'    => null
        ),
        'select' => array(
            'name'     => null,
            'size'     => null,
            'multiple' => array('multiple'),
            'disabled' => array('disabled'),
            'id'       => null,
            'class'    => null
        ),
        'label' => array(
            'for'   => null,
            'id'    => null,
            'class' => null
        )
    );

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Starts the form
     *
     * @access  public
     * @param   string  $action
     * @param   array   $options
     * @return  string
     */
    public function create($action = null, array $options = array())
    {
        if(is_string($action))
        {
            $attributes['action'] = URLPATH . $action;
        }
        else
        {
            $attributes['action'] = null;
        }

        foreach($options as $key => $value)
        {
            if(array_key_exists($key, $this->attributes['form']))
            {
                if(!is_array($value))
                {
                    if(isset($this->attributes['form'][$key]))
                    {
                        if(in_array($value, $this->attributes['form'][$key]))
                        {
                            switch($value)
                            {
                                case 'file':
                                    $attributes['enctype'] = 'multipart/form-data';
                                    break;
                                case 'get':
                                    $attributes['method'] = 'get';
                                    break;
                                default:
                                    $attributes[$key] = $value;
                                    break;
                            }
                        }
                    }
                    else
                    {
                        $attributes[$key] = $value;
                    }
                }
            }
        }

        if(!isset($attributes['method']))
        {
            $attributes['method'] = 'post';
        }

        $attrString = $this->getAttrAsString($attributes);

        return '<form' . $attrString . '>' . PHP_EOL;
    }

    /**
     * Create an input tag
     *
     * @access  public
     * @param   string  $name
     * @param   array   $options
     * @return  string
     */
    public function input($name = null, array $options = array())
    {
        $attributes = $this->setAttributes($options, 'input');

        if(!isset($attributes['type']))
        {
            $attributes['type'] = 'text';
        }

        if(isset($name) and is_string($name))
        {
            $attributes['name'] = $name;
        }

        $attrString = $this->getAttrAsString($attributes);

        $label = $this->getLabel($options, $name);

        return $label . '<input' . $attrString . ' />' . PHP_EOL;
    }

    /**
     * Creates a button tag
     *
     * @access  public
     * @param   string  $name
     * @param   string  $text
     * @param   array   $options
     * @return  string
     */
    public function button($name = null, $text = null, array $options = array())
    {
        $attributes = $this->setAttributes($options, 'button');

        if(!isset($attributes['type']))
        {
            $attributes['type'] = 'submit';
        }

        if(isset($name) and is_string($name))
        {
            $attributes['name'] = $name;
        }
        else
        {
            $name = $attributes['type'];
        }

        if(!isset($text))
        {
            $text = $name;
        }

        $attrString = $this->getAttrAsString($attributes);

        $label = $this->getLabel($options, $name);

        return $label
                . '<button' . $attrString . '>' . PHP_EOL
                . ' ' . $text . PHP_EOL
                . '</button>' . PHP_EOL;
    }

    /**
     * Creates a textarea
     *
     * @access  public
     * @param   string  $name
     * @param   string  $text
     * @param   array   $options
     * @return  string
     */
    public function textarea($name = null, $text = null, array $options = array())
    {
        $attributes = $this->setAttributes($options, 'textarea');

        if(!isset($attributes['rows']))
        {
            $attributes['rows'] = 8;
        }

        if(!isset($attributes['cols']))
        {
            $attributes['cols'] = 25;
        }

        if(isset($name) and is_string($name))
        {
            $attributes['name'] = $name;
        }

        $attrString = $this->getAttrAsString($attributes);

        $label = $this->getLabel($options, $name);

        if(isset($text) and is_string($text))
        {
            $textarea = $label
                      . '<textarea' . $attrString . '>' . PHP_EOL
                      . $text . PHP_EOL
                      . '</textarea>' . PHP_EOL;
        }
        else
        {
            $textarea = $label
                      . '<textarea' . $attrString . '></textarea>' . PHP_EOL;
        }

        return $textarea;
    }

    /**
     * Creates a select field
     *
     * @access  public
     * @param   string  $name
     * @param   array   $items
     * @param   array   $options
     * @return  string
     */
    public function select($name = null, array $items = array(), array $options = array())
    {
        $attributes = $this->setAttributes($options, 'select');

        if(isset($name) and is_string($name))
        {
            $attributes['name'] = $name;
        }

        $list = '';

        foreach($items as $key => $value)
        {
            if(is_array($value))
            {
                $list .= ' ' . '<optgroup label="' . $key . '">' . PHP_EOL;

                foreach($value as $key => $value)
                {
                    $list .= '  ' . '<option value="' . $key . '">'
                       . $value . '</option>' . PHP_EOL;
                }

                $list .= ' ' . '</optgroup>' . PHP_EOL;
            }
            else
            {
                $list .= ' ' . '<option value="' . $key . '">'
                       . $value . '</option>' . PHP_EOL;
            }
        }

        $attrString = $this->getAttrAsString($attributes);

        $label = $this->getLabel($options, $name);

        return $label
                . '<select' . $attrString . '>' . PHP_EOL
                . $list
                . '</select>' . PHP_EOL;
    }

    /**
     * Creates a label
     *
     * @access  public
     * @param   string  $for
     * @param   string  $text
     * @param   array   $options
     * @return  string
     */
    public function label($for = null, $text = null, array $options = array())
    {
        $attributes = $this->setAttributes($options, 'label');

        if(isset($for) and is_string($for))
        {
            $attributes['for'] = $for;
        }

        if(isset($text) and !is_string($text))
        {
            $text = null;
        }

        if(!isset($text))
        {
            $text = isset($attributes['for']) ? $attributes['for'] : null;
        }

        $attrString = $this->getAttrAsString($attributes);

        return '<label' . $attrString . '>'
               . $text . '</label>' . PHP_EOL;
    }

    /**
     * Closes the form
     *
     * @access  public
     * @return  string
     */
    public function close()
    {
        return '</form>' . PHP_EOL;
    }

    /**
     * Sets all the attributes
     *
     * @access  private
     * @param   array   $options
     * @param   string  $type
     * @return  array
     */
    private function setAttributes(array $options, $type)
    {
        $attributes = array();

        foreach($options as $key => $value)
        {
            if(array_key_exists($key, $this->attributes[$type]))
            {
                if(!is_array($value) or !is_bool($value))
                {
                    if(isset($this->attributes[$type][$key]))
                    {
                        if(in_array($value, $this->attributes[$type][$key]))
                        {
                            $attributes[$key] = $value;
                        }
                    }
                    else
                    {
                        $attributes[$key] = $value;
                    }
                }
            }
        }

        return $attributes;
    }

    /**
     * Gets an attribute string for tags
     *
     * @access  private
     * @param   array   $attributes
     * @return  string
     */
    private function getAttrAsString(array $attributes)
    {
        $string = null;

        foreach($attributes as $key => $value)
        {
            $string .= ' ' . $key . '="' . $value . '"';
        }

        return $string;
    }

    /**
     * Returns a label
     *
     * @access  private
     * @param   array   $options
     * @param   string  $name
     * @return  string
     */
    private function getLabel(array $options, $name)
    {
        if(isset($options['label']) and is_string($options['label']) and isset($name))
        {
            return '<label for="' . $name . '">'
                . $options['label'] . '</label>' . PHP_EOL;
        }

        return null;
    }
}
