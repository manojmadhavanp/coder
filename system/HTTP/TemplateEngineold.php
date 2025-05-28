<?php

namespace System\HTTP;

class TemplateEngineold
{
    private $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function setdata(array $data){
        $this->data = $data;
    }
    public function render($template)
    {
      // print_r($this->data);
      echo "<pre>";
        $template = $this->parseForeach($template);
        $template = $this->parseVariables($template);
      echo "</pre>";  
        return $template;
    }
    private function parseForeach($template)
    {
        
        //  $pattern = '/{foreach\((\$\w+)\)}(.*?){\/foreach}/s';
        //$pattern = '/{foreach\((\$\w+)\)}(.*?){\/foreach}/s';
          $pattern = '/{foreach\((\$\w+)\)}(.*?){\/foreach}/s';
          echo "<br />TEMPLATE FOR EACH:<br />";
          print_r($template);
        $template = preg_replace_callback($pattern, function ($matches) {
            echo "<br />**** MATCHES *****<br />";
            print_r($matches);
            $variable = str_replace('$', '', $matches[1]);
            $content = $matches[2];

            // Check if the variable exists in the data and is an array
            if (isset($this->data[$variable]) && is_array($this->data[$variable])) {
                $result = '';
                foreach ($this->data[$variable] as $item) {
                    // Copy the variable and its content for each iteration
                    $dataCopy = array();
                    $dataCopy[$variable] = $item;
                    print_r($dataCopy);
                    print_r($content);
                    // Parse the loop content for each item in the array
                    $parsedContent = $this->parseVariables($content, $dataCopy);
                    $result .= $parsedContent;
                }
                return $result;
            }

            return $matches[0];
        }, $template);

        return $template;
    }

    /* private function parseForeach($template)
    {
        $pattern = '/{foreach\((\$\w+)\)}(.*?){\/foreach}/s';
        $template = preg_replace_callback($pattern, function ($matches) {
            echo "<pre>";
            
            $variable = str_replace('$', '', $matches[1]);;
            $content = $matches[2];
            

            $result = '';
            if (isset($this->data[$variable]) && is_array($this->data[$variable])) {
                foreach ($this->data[$variable] as $item) {
                    $dataCopy = array();
                    $dataCopy[$variable] = $item;
                    print_r($dataCopy);
                    print_r($content);
                    $engine = new TemplateEngine($dataCopy);
                    $engine->setdata($dataCopy);
                    $result .= $engine->parseVariables($content);
                }
            }

            return $result;
        }, $template);

        return $template;
    }*/

    private function parseVariables($template, $data = null)
    {
        $data = $data ?: $this->data;

        //  $pattern = '/{\$((?:\w+->)*\w+)}/';
        //  $pattern = '/{\$((?:\w+(?:->\w+|\[\'\w+\'\]))+)}/';
        //  $pattern = '/{\$((?:\w+|\w+(?:->\w+|\[\'\w+\'\]))+)}/';
        //  $pattern = '/{\$((?:\w+(?:->\w+|\[\'\w+\'\])*)+)}/';
        //  $pattern = '/{\$((?:\w+(?:->\w+|\[\'.*?\'\])*)+)}/';
        //  $pattern = '/{\$((?:\w+(?:->\w+|\[\'[^\']+\'])*)+)}/';
        //    $pattern = '/{\$((?:\w+(?:->\w+|\[\'.*?\'\]|(?:\[\'.*?\'\]|\[\d+\])*)*)+)}/';
        // $pattern = '/{\$((?:\w+(?:->\w+|\[\'.*?\'\]|{foreach\(\$.*?\)}.*?{\/foreach\})*)+)}/';
        //$pattern = '/{\$((?:\w+(?:\[[\'"]\w+[\'"]\])*))/';
        // $pattern = '/{\$((?:\w+(?:\[[\'"]\w+[\'"]\])*(?:->\w+(?:\[[\'"]\w+[\'"]\])*)*))}/';
        $pattern = '/{\$((?:\w+(?:->\w+|\[[\'"]\w+[\'"]\])*(?:->\w+(?:\[[\'"]\w+[\'"]\])*)*))}/';

        echo "<pre>";
        echo "<br />***********<br />";
        echo "TEMPLATE:<br />";
        echo $template;
        echo "<br />TEMPLATE END:";
        echo "<br />DATA:<br />";
        print_r($this->data);
        echo "<br />DATA END:";
        $template = preg_replace_callback($pattern, function ($matches) use ($data) {
            $variable = $matches[1];
           
            echo "ParseVariables start<br />";
            print_r($matches);
           
           // print_r($variable);
            //echo "<br />";
           // print_r($this->data);
            echo "<br />";
            if (strpos($variable, 'foreach') !== false) {
                //   echo "<br />Has foreach so returning";
                //   print_r($matches[0]);
                return $this->parseForeach("{{$variable}}");
            }
            if (isset($this->data)) {
                $value = $this->getValueFromVariable($data, $variable);
            //    echo "<br />VALUE:";
            //    print_r($value);
                return $value;
            }

            return $matches[0];
        }, $template);
        print_r($template);
        echo "<br />ParseVariables end";
        echo "<br />***********<br />";
        echo "</pre>";
        
        return $template;
    }

    /* private function getValueFromVariable($data, $variable)
    {
        $parts = explode('->', $variable);
        echo "<pre>";
        echo "<br />***********<br />";
        print_r($parts);

        foreach ($parts as $part) {
            if (is_array($data) && isset($data[$part])) {
                $data = $data[$part];
            } elseif (is_object($data) && isset($data->$part)) {
                $data = $data->$part;
            } else {
                return '';
            }
        }

        return $data;
    }
    private function getValueFromVariable($data, $variable)
    {
        $parts = explode('->', $variable);
        echo "<br />PARTS:<br />";
        print_r($parts);

        foreach ($parts as $part) {
            if (preg_match('/^(\w+)\[\'(\w+)\'\]$/', $part, $arrayMatches)) {
                list(, $arrayName, $key) = $arrayMatches;
                if (isset($data[$arrayName][$key])) {
                    $data = $data[$arrayName][$key];
                } else {
                    return '';
                }
            } elseif (is_array($data) && isset($data[$part])) {
                $data = $data[$part];
            } elseif (is_object($data) && isset($data->$part)) {
                $data = $data->$part;
            } else {
                return '';
            }
        }
        echo $data;
        echo "<br />PARTS END:<br />";
        return $data;
    }

    private function getValueFromVariable($data, $variable)
    {
        $parts = explode('->', $variable);

        foreach ($parts as $part) {
            // Handle array access with index in square brackets, e.g., $data['key']
            if (preg_match('/^(\w+)\[\'([^\']+)\'\]$/', $part, $arrayMatches)) {
                list(, $arrayName, $key) = $arrayMatches;
                if (isset($data[$arrayName][$key])) {
                    $data = $data[$arrayName][$key];
                } else {
                    return '';
                }
            }
            // Handle array access with index in square brackets as integer, e.g., $data[0]
            elseif (preg_match('/^(\w+)\[(\d+)\]$/', $part, $arrayMatches)) {
                list(, $arrayName, $index) = $arrayMatches;
                if (isset($data[$arrayName][$index])) {
                    $data = $data[$arrayName][$index];
                } else {
                    return '';
                }
            }
            // Handle object properties, e.g., $data->property
            elseif (is_object($data) && isset($data->$part)) {
                $data = $data->$part;
            }
            // Handle regular array access, e.g., $data['key'] or $data[0]
            elseif (is_array($data) && isset($data[$part])) {
                $data = $data[$part];
            } else {
                return '';
            }
        }

        return $data;
    }
    */
    private function getValueFromVariable($data, $variable)
    {
        $parts = explode('->', $variable);

        foreach ($parts as $part) {
            if (preg_match('/^(\w+)\[\'(\w+)\'\]$/', $part, $arrayMatches)) {
                list(, $arrayName, $key) = $arrayMatches;
                if (isset($data[$arrayName][$key])) {
                    $data = $data[$arrayName][$key];
                } else {
                    return '';
                }
            } elseif (is_array($data) && isset($data[$part])) {
                $data = $data[$part];
            } elseif (is_object($data) && isset($data->$part)) {
                $data = $data->$part;
            } else {
                return '';
            }
        }

        return $data;
    }

}


?>