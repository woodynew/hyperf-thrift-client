<?php

namespace tests\services\SumService;

/**
 * Autogenerated by Thrift Compiler (0.12.0)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 * @generated
 */

use Thrift\Base\TBase;
use Thrift\Type\TType;
use Thrift\Type\TMessageType;
use Thrift\Exception\TException;
use Thrift\Exception\TProtocolException;
use Thrift\Protocol\TProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Exception\TApplicationException;

class SumService_sum_args
{
    static public $isValidate = false;

    static public $_TSPEC = array(
        1 => array(
            'var' => 'a',
            'isRequired' => true,
            'type' => TType::I32,
        ),
        2 => array(
            'var' => 'b',
            'isRequired' => true,
            'type' => TType::I32,
        ),
    );

    /**
     * @var int
     */
    public $a = null;
    /**
     * @var int
     */
    public $b = null;

    public function __construct($vals = null)
    {
        if (is_array($vals)) {
            if (isset($vals['a'])) {
                $this->a = $vals['a'];
            }
            if (isset($vals['b'])) {
                $this->b = $vals['b'];
            }
        }
    }

    public function getName()
    {
        return 'SumService_sum_args';
    }


    public function read($input)
    {
        $xfer = 0;
        $fname = null;
        $ftype = 0;
        $fid = 0;
        $xfer += $input->readStructBegin($fname);
        while (true) {
            $xfer += $input->readFieldBegin($fname, $ftype, $fid);
            if ($ftype == TType::STOP) {
                break;
            }
            switch ($fid) {
                case 1:
                    if ($ftype == TType::I32) {
                        $xfer += $input->readI32($this->a);
                    } else {
                        $xfer += $input->skip($ftype);
                    }
                    break;
                case 2:
                    if ($ftype == TType::I32) {
                        $xfer += $input->readI32($this->b);
                    } else {
                        $xfer += $input->skip($ftype);
                    }
                    break;
                default:
                    $xfer += $input->skip($ftype);
                    break;
            }
            $xfer += $input->readFieldEnd();
        }
        $xfer += $input->readStructEnd();
        return $xfer;
    }

    public function write($output)
    {
        $xfer = 0;
        $xfer += $output->writeStructBegin('SumService_sum_args');
        if ($this->a !== null) {
            $xfer += $output->writeFieldBegin('a', TType::I32, 1);
            $xfer += $output->writeI32($this->a);
            $xfer += $output->writeFieldEnd();
        }
        if ($this->b !== null) {
            $xfer += $output->writeFieldBegin('b', TType::I32, 2);
            $xfer += $output->writeI32($this->b);
            $xfer += $output->writeFieldEnd();
        }
        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }
}
