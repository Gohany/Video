<?php

/* This file is dedicated to the public domain; you may do as you wish with it. */

// Information needed to parse an element type
class EBMLElementType
{

        public $name;
        public $datatype;
        public $validParents;

}

// Information needed to parse all possible element types in a document
class EBMLElementTypeList
{

        public $_els;
        public $_ids;

        public function __construct($filename)
        {
                $lines = file($filename);
                foreach ($lines as $line)
                {
                        $fields = explode(' ', trim($line));
                        $t = new EBMLElementType;
                        $id = hexdec($fields[0]);
                        $t->datatype = $fields[1];
                        $t->name = $fields[2];
                        $t->validParents = array();
                        for ($i = 0; $i + 3 < count($fields); $i++)
                        {
                                if ($fields[$i + 3] == '*' || $fields[$i + 3] == 'root')
                                {
                                        $t->validParents[$i] = $fields[$i + 3];
                                }
                                else
                                {
                                        $t->validParents[$i] = hexdec($fields[$i + 3]);
                                }
                        }
                        $this->_els[$id] = $t;
                        $this->_ids[strtoupper($t->name)] = $id;
                }
        }

        public function exists($id)
        {
                return isset($this->_els[$id]);
        }

        public function name($id)
        {
                if (!isset($this->_els[$id]))
                        return NULL;
                return $this->_els[$id]->name;
        }

        public function id($name)
        {
                $name = strtoupper($name);
                if (!isset($this->_ids[$name]))
                        return NULL;
                return $this->_ids[$name];
        }

        public function datatype($id)
        {
                if ($id == 'root')
                        return 'container';
                if (!isset($this->_els[$id]))
                        return 'binary';
                return $this->_els[$id]->datatype;
        }

        public function validChild($id1, $id2)
        {
                if (!isset($this->_els[$id2]))
                        return TRUE;
                $parents = $this->_els[$id2]->validParents;
                return in_array('*', $parents) || in_array($id1, $parents);
        }

}

// Matroska element types
global $EBML_ELEMENTS;
$EBML_ELEMENTS = new EBMLElementTypeList(dirname(__FILE__) . '/matroska-elements.txt');

// Decode big-endian integer
function ebmlDecodeInt($data, $signed = FALSE, $carryIn = 0)
{
        $n = $carryIn;
        if (strlen($data) > 8)
                throw new Exception('not supported: integer too long');
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

// Decode big-endian IEEE float
function ebmlDecodeFloat($data)
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
                                        return NULL;
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
                                        return NULL;
                        }
                default:
                        error_log('unsupported float length');
                        return NULL;
        }
}

// Decode big-endian signed offset from Jan 01, 2000 in nanoseconds
// Convert to offset from Jan 01, 1970 in seconds
function ebmlDecodeDate($data)
{
        return ebmlDecodeInt($data, TRUE) * 1e-9 + 946684800;
}

// Decode data of specified datatype
function ebmlDecode($data, $datatype)
{
        switch ($datatype)
        {
                case 'int': return ebmlDecodeInt($data, TRUE);
                case 'uint': return ebmlDecodeInt($data, FALSE);
                case 'float': return ebmlDecodeFloat($data);
                case 'string': return chop($data, "\0");
                case 'date': return ebmlDecodeDate($data);
                case 'binary': return $data;
                default: throw new Exception('unknown datatype');
        }
}

class fileHandle
{

        public static $instance;
        public $bin = '';
        public $position = 0;
        public $removed = 0;

        public static function singleton()
        {
                self::$instance || self::$instance = new fileHandle();
                return self::$instance;
        }

        public static function read($handle, $size)
        {
                if ((self::singleton()->position() + $size) > strlen(self::singleton()->bin))
                {
                        while ((self::singleton()->position() + $size) > strlen(self::singleton()->bin) && !feof($handle))
                        {
                                $bin = fread($handle, 76800);
//                                print "BINARY! " . PHP_EOL;
//                                print bin2hex($bin) . PHP_EOL;
//                                exit;
                                self::singleton()->bin .= $bin;
                        }
                }
                $data = substr(self::singleton()->bin, self::singleton()->position(), $size);
                self::singleton()->position += $size;
                
                if (self::singleton()->position() > 1500000)
                {
                        print "MEM USAGE: ". memory_get_usage() . PHP_EOL;
                        print "BIN LENGTH: " . strlen(self::singleton()->bin) . PHP_EOL;
                        self::singleton()->bin = substr(self::singleton()->bin, 750000);
                        self::singleton()->removed += 750000;
                        //gc_collect_cycles();
                        print "REMOVING.. " . PHP_EOL;
                        print "BIN LENGTH: " . strlen(self::singleton()->bin) . PHP_EOL;
                        print "MEM USAGE: ". memory_get_usage() . PHP_EOL;
                        
                }
                
                return $data;
        }
        
        public static function position()
        {
                return self::singleton()->position - self::singleton()->removed;
        }
        
        public static function seek($handle, $position)
        {
                self::singleton()->position = $position;
        }

        public static function bin()
        {
                return self::singleton()->bin;
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

        public function __construct($fileHandle, $offset = 0, $size = NULL)
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

        // Total size of data section (NULL if unknown)
        public function size()
        {
                return $this->_size;
        }

        // Set end of data section
        public function setSize($size)
        {
                if ($this->_size === NULL)
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
                if ($this->_size === NULL)
                {
                        fileHandle::seek($this->_fileHandle, $this->_offset + $this->_position);
                        fileHandle::read($this->_fileHandle, 1);
                        //fseek($this->_fileHandle, $this->_offset + $this->_position);
                        //fread($this->_fileHandle, 1);
                        if (feof($this->_fileHandle))
                        {
                                //return TRUE;
                                $this->_size = $this->_position;
                                return TRUE;
                        }
                        else
                        {
                                return FALSE;
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
                if ($size !== NULL)
                {
                        $this->_position += $size;
                        if ($this->_size !== NULL && $this->_position > $this->_size)
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
                        return '';
                if ($this->_size === NULL)
                        throw new Exception('unknown length');
                fileHandle::seek($this->_fileHandle, $this->_offset);
                $data = fileHandle::read($this->_fileHandle, $this->_size);
                //fseek($this->_fileHandle, $this->_offset);
                //$data = fread($this->_fileHandle, $this->_size);
                if ($data === FALSE || strlen($data) != $this->_size)
                {
                        print "WANTED " . $this->_size . " but got " . strlen($data) . PHP_EOL;
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
        public function readVarInt($signed = FALSE)
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
                        print "hmmm" . PHP_EOL;
                        return NULL;
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
                $n = ebmlDecodeInt($rawInt, FALSE, $n);
                var_dump($n);
                
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
                global $EBML_ELEMENTS;
                $this->_id = $id;
                $this->_name = $EBML_ELEMENTS->name($this->_id);
                $this->_datatype = $EBML_ELEMENTS->datatype($this->_id);
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
                                return ebmlDecode($this->_content->readAll(), $this->_datatype);
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
                        return NULL;
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
                if ($this->content()->size() !== NULL && $this->_position > $this->content()->size())
                {
                        throw new Exception('unexpected end of data');
                }
                unset($this->_cache[$position]);
        }

        public function valid()
        {
                global $EBML_ELEMENTS;
                if (isset($this->_cache[$this->_position]))
                        return TRUE;
                $this->content()->setPosition($this->_position);
                if ($this->content()->endOfData())
                        return FALSE;

                $this->content()->head = '';
                $id = $this->content()->readVarInt();
                if ($id === NULL)
                        throw new Exception('invalid ID');
                if ($this->content()->size() === NULL && !$EBML_ELEMENTS->validChild($this->id(), $id))
                {
                        $this->content()->setSize($this->_position);
                        return FALSE;
                }
                $size = $this->content()->readVarInt();
                $headSize = $this->content()->position() - $this->_position;
                $content = $this->content()->nextSlice($size);
                print "TYPE: ".$EBML_ELEMENTS->datatype($id).PHP_EOL;
                if ($EBML_ELEMENTS->datatype($id) == 'container')
                {
                        $element = new EBMLElementList($id, $content, $headSize, $this->content()->head);
                }
                else
                {
                        if ($size === NULL)
                        {
                                throw new Exception('non-container element of unknown size');
                        }
                        $element = new EBMLElement($id, $content, $headSize, $this->content()->head);
                }
                $this->_cache[$this->_position] = $element;
                return TRUE;
        }

        // Total size of element (including ID and size)
        public function size()
        {
                if ($this->content()->size() === NULL)
                {
                        $iElement = 0;
                        foreach ($this as $element)
                        { // iterate over elements to find end
                                $iElement++;
                                if ($iElement > self::$MAX_ELEMENTS)
                                        throw new Exception('not supported: too many elements');
                        }
                }
                return $this->headSize() + $this->content()->size();
        }

        // Read and interpret content
        public function value()
        {
                return $this;
        }

        // Get element value by name
        public function get($name, $defaultValue = NULL)
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

// Parse block
class MatroskaBlock
{

        const LACING_NONE = 0;
        const LACING_XIPH = 1;
        const LACING_EBML = 3;
        const LACING_FIXED = 2;

        public $trackNumber;
        public $timecode;
        public $keyframe;
        public $invisible;
        public $lacing;
        public $discardable;
        public $frames;

        public function __construct($reader)
        {
                # Header
                $this->trackNumber = $reader->readVarInt();
                $this->timecode = ebmlDecodeInt($reader->read(2), TRUE);
                $flags = ord($reader->read(1));
                if (($flags & 0x70) != 0)
                {
                        throw new Exception('reserved flags set');
                }
                $this->keyframe = (($flags & 0x80) != 0);
                $this->invisible = (($flags & 0x08) != 0);
                $this->lacing = ($flags >> 1) & 0x03;
                $this->discardable = (($flags & 0x01) != 0);

                # Lacing sizes
                if ($this->lacing == self::LACING_NONE)
                {
                        $nsizes = 0;
                }
                else
                {
                        $nsizes = ord($reader->read(1));
                }
                $sizes = array();
                switch ($this->lacing)
                {
                        case self::LACING_XIPH:
                                for ($i = 0; $i < $nsizes; $i++)
                                {
                                        $size = 0;
                                        $x = 255;
                                        while ($x == 255)
                                        {
                                                $x = ord($reader->read(1));
                                                $size += $x;
                                                if ($size > 65536)
                                                        throw new Exception('not supported: laced frame too long');
                                        }
                                        $sizes[$i] = $size;
                                }
                                break;
                        case self::LACING_EBML:
                                $size = 0;
                                for ($i = 0; $i < $nsizes; $i++)
                                {
                                        $dsize = $reader->readVarInt($i != 0);
                                        if ($dsize === NULL || $size + $dsize < 0)
                                        {
                                                throw new Exception('invalid frame size');
                                        }
                                        $size += $dsize;
                                        $sizes[$i] = $size;
                                }
                                break;
                        case self::LACING_FIXED:
                                $lenRemaining = $reader->size() - $reader->position();
                                if ($lenRemaining % ($nsizes + 1) != 0)
                                {
                                        throw new Exception('data size not divisible by frame count');
                                }
                                $size = (int) ($lenRemaining / ($nsizes + 1));
                                for ($i = 0; $i < $nsizes; $i++)
                                {
                                        $sizes[$i] = $size;
                                }
                                break;
                }

                # Frames
                $this->frames = array();
                for ($i = 0; $i < $nsizes; $i++)
                {
                        $this->frames[$i] = $reader->nextSlice($sizes[$i]);
                }
                $this->frames[$nsizes] = $reader->nextSlice($reader->size() - $reader->position());
        }

}

// Create element list from $fileHandle
function readMatroska($fileHandle)
{
        $reader = new EBMLReader($fileHandle);
        if ($reader->read(4) != "\x1a\x45\xdf\xa3")
        {
                throw new Exception('not an EBML file');
        }
        $root = new EBMLElementList('root', $reader, 0);
        var_dump($root);
        $header = $root->get('EBML');
        var_dump($header);
        $ebmlVersion = $header->get('EBMLReadVersion', 1);
        $docType = $header->get('DocType');
        $docTypeVersion = $header->get('DocTypeReadVersion', 1);
        if ($ebmlVersion != 1)
        {
                throw new Exception('unsupported EBML version');
        }
        if ($docType != 'matroska' && $docType != 'webm')
        {
                throw new Exception('unsupported document type');
        }
        if ($docTypeVersion < 1 || $docTypeVersion > 4)
        {
                throw new Exception('unsupported document type version');
        }
        return $root;
}

function readAll($fileHandle)
{
        $reader = new EBMLReader($fileHandle);
        $root = new EBMLElementList('root', $reader, 0, '');

        $context = new ZMQContext();
        $publisher = $context->getSocket(ZMQ::SOCKET_PUB);
        $publisher->bind("tcp://*:5556");
        
        $handle = fopen('/var/www/mkv.header', 'w');
        flock($handle, LOCK_EX);
        
        iterateElements($handle, $publisher, $root, 0, 0);
        var_dump($root);
        print "SIZE: ".strlen(serialize($root)).PHP_EOL;
}

function iterateElements($handle, &$publisher, &$elements, $depth, $clusters = 0, $skip = false)
{
        $pub = 'mkv';
        $simpleBlocks = 0;
        foreach ($elements as $element)
        {
                print get_class($element) . PHP_EOL;
                switch (get_class($element))
                {
                        case 'EBMLElementList':
                                
                                if ($clusters == 0 && $element->name() == 'Cluster')
                                {
                                        $clusters++;
                                        flock($handle, LOCK_UN);
                                }
                                elseif ($clusters == 0)
                                {
                                        //print "WRITING CONTAINER: ".$element->name() . PHP_EOL;
                                        fwrite($handle, $element->_head);
                                }
                                
                                if ($clusters > 0)
                                {
                                        //print "SENDING CONTAINER: ".$element->name() . PHP_EOL;
                                        $publisher->send($pub . 1 . $element->_head);
                                }
                                else
                                {
                                        //print "SKIPPING: ".$element->name() . PHP_EOL;
                                }
                                //print $element->_head;
                                iterateElements($handle, $publisher, $element, $depth + 1, $clusters, $skip);
                                break;
                        case 'EBMLElement':
                                
                                if ($clusters > 0)
                                {
                                        $publisher->send($pub . 0 . $element->_head . $element->_content->readAll());
                                        //print "SENDING: ".$element->name() . PHP_EOL;
                                }
                                else
                                {
                                        //print "WRITING: ".$element->name() . PHP_EOL;
                                        fwrite($handle, $element->_head . $element->_content->readAll());
                                }
                                unset($element);
                                //print $element->_head;
                                //print $element->_content->readAll();
                                break;
                }
                unset($element);
        }
        unset($elements);
        
}

function ebmlEncodeVarInt($n)
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

function ebmlEncodeElementName($name)
{
        global $EBML_ELEMENTS;
        return ebmlEncodeVarInt($EBML_ELEMENTS->id($name));
}

function ebmlEncodeElement($name, $content)
{
        return ebmlEncodeElementName($name) . ebmlEncodeVarInt(strlen($content)) . $content;
}
