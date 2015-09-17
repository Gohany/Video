<?php

class ebmlUtil
{

        // Decode big-endian signed offset from Jan 01, 2000 in nanoseconds
        // Convert to offset from Jan 01, 1970 in seconds
        public static function ebmlDecodeDate($data)
        {
                return self::ebmlDecodeInt($data, true) * 1e-9 + 946684800;
        }

        // Decode data of specified datatype
        public static function ebmlDecode($data, $datatype)
        {
                switch ($datatype)
                {
                        case 'int': return self::ebmlDecodeInt($data, true);
                        case 'uint': return self::ebmlDecodeInt($data, false);
                        case 'float': return self::ebmlDecodeFloat($data);
                        case 'string': return chop($data, "\0");
                        case 'date': return self::ebmlDecodeDate($data);
                        case 'binary': return $data;
                        default: throw new Exception('unknown datatype');
                }
        }

        // Decode big-endian IEEE float
        public static function ebmlDecodeFloat($data)
        {
                switch (strlen($data))
                {
                        case 0:
                                return 0;
                        case 4:
                                switch (pack('f', 1e9))
                                {
                                        case '(knN':
                                                $arr = unpack('f', strrev($data));
                                                return $arr[1];
                                        case 'Nnk(':
                                                $arr = unpack('f', $data);
                                                return $arr[1];
                                        default:
                                                error_log('cannot decode floats');
                                                return null;
                                }
                        case 8:
                                switch (pack('d', 1e9))
                                {
                                        case "\x00\x00\x00\x00\x65\xcd\xcd\x41":
                                                $arr = unpack('d', strrev($data));
                                                return $arr[1];
                                        case "\x41\xcd\xcd\x65\x00\x00\x00\x00":
                                                $arr = unpack('d', $data);
                                                return $arr[1];
                                        default:
                                                error_log('cannot decode floats');
                                                return null;
                                }
                        default:
                                error_log('unsupported float length');
                                return null;
                }
        }

        // Decode big-endian integer
        public static function ebmlDecodeInt($data, $signed = false, $carryIn = 0)
        {
                $n = $carryIn;
                if (strlen($data) > 8)
                {
                        throw new Exception('not supported: integer too long');
                }
                for ($i = 0; $i < strlen($data); $i++)
                {
                        if ($n > (PHP_INT_MAX >> 8) || $n < ((-PHP_INT_MAX - 1) >> 8))
                        {
                                $n = floatval($n);
                        }
                        $n = $n * 0x100 + ord($data[$i]);
                        if ($i == 0 && $signed && ($n & 0x80) != 0)
                        {
                                $n -= 0x100;
                        }
                }
                return $n;
        }

        public static function ebmlEncodeVarInt($n)
        {
                $data = '';
                $flag = 0x80;
                while ($n >= $flag)
                {
                        if ($flag == 0)
                        {
                                throw new Exception('not supported: number too large');
                        }
                        $data = chr($n & 0xFF) . $data;
                        $n = $n >> 8;
                        $flag = $flag >> 1;
                }
                $data = chr($n | $flag) . $data;
                return $data;
        }

        public static function ebmlEncodeElementName($name)
        {
                return self::ebmlEncodeVarInt(EBMLElements::id($name));
        }

        public static function ebmlEncodeElement($name, $content)
        {
                return self::ebmlEncodeElementName($name) . self::ebmlEncodeVarInt(strlen($content)) . $content;
        }
        
        public static function bitwiseAdd($x, $y)
        {
                if (bindec($y) == 0)
                        return $x;
                else
                        return self::bitwiseAdd ($x ^ $y, ($x & $y) << 1);
        }

}

// Methods for reading data from section of EBML file
class EBMLReader
{

        public $_fileHandle;
        public $_offset;
        public $_size;
        public $_position;
        public $head = '';

        public function __construct($fileHandle, $offset = 0, $size = null)
        {
                $this->_fileHandle = $fileHandle;
                $this->_offset = $offset;
                $this->_size = $size;
                $this->_position = 0;
        }

        // Tell position within data section
        public function position()
        {
                return $this->_position;
        }

        // Set position within data section
        public function setPosition($position)
        {
                $this->_position = $position;
        }

        // Total size of data section (null if unknown)
        public function size()
        {
                return $this->_size;
        }

        // Set end of data section
        public function setSize($size)
        {
                if ($this->_size === null)
                {
                        $this->_size = $size;
                }
                else
                {
                        throw new Exception('size already set');
                }
        }

        // Determine whether we are at end of data
        public function endOfData()
        {
                //return false;
                if ($this->_size === null)
                {
                        fileHandle::seek($this->_fileHandle, $this->_offset + $this->_position);
                        fileHandle::read($this->_fileHandle, 1);
                        if (feof($this->_fileHandle))
                        {
                                //return true;
                                $this->_size = $this->_position;
                                return true;
                        }
                        else
                        {
                                return false;
                        }
                }
                else
                {
                        return $this->_position >= $this->_size;
                }
        }

        // Create EBMLReader containing $size bytes and advance
        public function nextSlice($size)
        {
                $slice = new EBMLReader($this->_fileHandle, $this->_offset + $this->_position, $size);
                if ($size !== null)
                {
                        $this->_position += $size;
                        if ($this->_size !== null && $this->_position > $this->_size)
                        {
                                throw new Exception('unexpected end of data');
                        }
                }
                return $slice;
        }

        // Read entire region
        public function readAll()
        {
                if ($this->_size == 0)
                {
                        return '';
                }
                if ($this->_size === null)
                {
                        throw new Exception('unknown length');
                }

                fileHandle::seek($this->_fileHandle, $this->_offset);
                $data = fileHandle::read($this->_fileHandle, $this->_size);

                if ($data === false || strlen($data) != $this->_size)
                {
                        throw new Exception('error reading from file');
                }
                return $data;
        }

        // Read $size bytes
        public function read($size)
        {
                return $this->nextSlice($size)->readAll();
        }

        // Read variable-length integer
        public function readVarInt($signed = false)
        {
                // Read size and remove flag
                $ord = $this->read(1);
                $n = ord($ord);
                $this->head .= $ord;

                $size = 0;
                if ($n == 0)
                {
                        throw new Exception('not supported: variable-length integer too long');
                }
                $flag = 0x80;
                while (($n & $flag) == 0)
                {
                        $flag = $flag >> 1;
                        $size++;
                }
                $n -= $flag;

                // Read remaining data
                $rawInt = $this->read($size);
                $this->head .= $rawInt;

                // Check for all ones
                if ($n == $flag - 1 && $rawInt == str_repeat("\xFF", $size))
                {
                        return null;
                }

                // Range shift for signed integers
                if ($signed)
                {
                        if ($flag == 0x01)
                        {
                                $n = ord($rawInt[0]) - 0x80;
                                $rawInt = $rawInt . substr(1);
                        }
                        else
                        {
                                $n -= ($flag >> 1);
                        }
                }

                // Convert to integer
                $n = ebmlUtil::ebmlDecodeInt($rawInt, false, $n);
                
                // Range shift for signed integers
                if ($signed)
                {
                        if ($n == PHP_INT_MAX)
                        {
                                $n = floatval($n);
                        }
                        $n++;
                }

                return $n;
        }

}

// EBML element
class EBMLElement
{

        public $_id;
        public $_name;
        public $_datatype;
        public $_content;
        public $_headSize;
        public $_head = '';

        public function __construct($id, $content, $headSize, $head)
        {
                $this->_id = $id;
                $this->_name = EBMLElements::name($this->_id);
                $this->_datatype = EBMLElements::datatype($this->_id);
                $this->_content = $content;
                $this->_headSize = $headSize;
                $this->_head = $head;
        }

        public function id()
        {
                return $this->_id;
        }

        public function name()
        {
                return $this->_name;
        }

        public function datatype()
        {
                return $this->_datatype;
        }

        public function content()
        {
                return $this->_content;
        }

        public function headSize()
        {
                return $this->_headSize;
        }

        // Total size of element (including ID and datasize)
        public function size()
        {
                return $this->_headSize + $this->_content->size();
        }

        // Read and interpret content
        public function value($decode = true)
        {
                if ($this->_datatype == 'binary')
                {
                        return $this->_content;
                }
                else
                {
                        if ($decode)
                        {
                                return ebmlUtil::ebmlDecode($this->_content->readAll(), $this->_datatype);
                        }
                        else
                        {
                                return $this->_content->readAll();
                        }
                }
        }

}

// Iterate over EBML elements in data
class EBMLElementList extends EBMLElement implements Iterator
{

        public $_cache;
        public $_position;
        public static $MAX_ELEMENTS = 10000;

        public function __construct($id, $content, $headSize, $head)
        {
                parent::__construct($id, $content, $headSize, $head);
                $this->_cache = array();
                $this->_position = 0;
        }

        public function rewind()
        {
                $this->_position = 0;
        }

        public function current()
        {
                if ($this->valid())
                {
                        return $this->_cache[$this->_position];
                }
                else
                {
                        return null;
                }
        }

        public function key()
        {
                return $this->_position;
        }

        public function next()
        {
                $position = $this->_position;
                $this->_position += $this->current()->size();
                if ($this->content()->size() !== null && $this->_position > $this->content()->size())
                {
                        throw new Exception('unexpected end of data');
                }
                unset($this->_cache[$position]);
        }

        public function valid()
        {

                if (isset($this->_cache[$this->_position]))
                {
                        return true;
                }

                $this->content()->setPosition($this->_position);
                if ($this->content()->endOfData())
                {
                        return false;
                }

                $this->content()->head = '';
                $id = $this->content()->readVarInt();
                if ($id === null)
                {
                        throw new Exception('invalid ID');
                }
                
                if ($this->content()->size() === null && !EBMLElements::validChild($this->id(), $id))
                {
                        $this->content()->setSize($this->_position);
                        return false;
                }

                $size = $this->content()->readVarInt();
                
                $headSize = $this->content()->position() - $this->_position;
                $content = $this->content()->nextSlice($size);
                
                if (EBMLElements::datatype($id) == 'container')
                {
                        $element = new EBMLElementList($id, $content, $headSize, $this->content()->head);
                }
                else
                {
                        if ($size === null)
                        {
                                throw new Exception('non-container element of unknown size');
                        }
                        $element = new EBMLElement($id, $content, $headSize, $this->content()->head);
                }
                $this->_cache[$this->_position] = $element;
                return true;
        }

        // Total size of element (including ID and size)
        public function size()
        {
                if ($this->content()->size() === null)
                {
                        $iElement = 0;
                        foreach ($this as $element)
                        {
                                // iterate over elements to find end
                                $iElement++;
                                if ($iElement > self::$MAX_ELEMENTS)
                                        throw new Exception('not supported: too many elements');
                        }
                }
                return $this->headSize() + $this->content()->size();
        }

        // Read and interpret content
        public function value($decode = true)
        {
                return $this;
        }

        // Get element value by name
        public function get($name, $defaultValue = null)
        {
                $iElement = 0;
                foreach ($this as $element)
                {
                        $iElement++;
                        if ($iElement > self::$MAX_ELEMENTS)
                                throw new Exception('not supported: too many elements');
                        if (strtoupper($element->name()) == strtoupper($name))
                        {
                                return $element->value();
                        }
                }
                return $defaultValue;
        }

}
