<?php

namespace SwooleKit\Cache\Memcache;

use EasySwoole\Spl\SplBean;

/**
 * 二进制协议包
 * Class Protocol
 * @package Memcache
 * @see https://github.com/memcached/memcached/wiki/BinaryProtocolRevamped
 */
class Protocol extends SplBean
{
    // for header
    protected $magic;
    protected $opcode;
    protected $dataType;
    protected $status;
    protected $opaque;
    protected $cas1;
    protected $cas2;

    // for body
    protected $extras;
    protected $key;
    protected $value;

    /**
     * Magic Getter
     * @return mixed
     */
    public function getMagic()
    {
        return $this->magic;
    }

    /**
     * Magic Setter
     * @param mixed $magic
     * @return Protocol
     */
    public function setMagic($magic)
    {
        $this->magic = $magic;
        return $this;
    }

    /**
     * Opcode Getter
     * @return mixed
     */
    public function getOpcode()
    {
        return $this->opcode;
    }

    /**
     * Opcode Setter
     * @param mixed $opcode
     * @return Protocol
     */
    public function setOpcode($opcode)
    {
        $this->opcode = $opcode;
        return $this;
    }

    /**
     * DataType Getter
     * @return mixed
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * DataType Setter
     * @param mixed $dataType
     * @return Protocol
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;
        return $this;
    }

    /**
     * Status Getter
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Status Setter
     * @param mixed $status
     * @return Protocol
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Opaque Getter
     * @return mixed
     */
    public function getOpaque()
    {
        return $this->opaque;
    }

    /**
     * Opaque Setter
     * @param mixed $opaque
     * @return Protocol
     */
    public function setOpaque($opaque)
    {
        $this->opaque = $opaque;
        return $this;
    }

    /**
     * Cas1 Getter
     * @return mixed
     */
    public function getCas1()
    {
        return $this->cas1;
    }

    /**
     * Cas1 Setter
     * @param mixed $cas1
     * @return Protocol
     */
    public function setCas1($cas1)
    {
        $this->cas1 = $cas1;
        return $this;
    }

    /**
     * Cas2 Getter
     * @return mixed
     */
    public function getCas2()
    {
        return $this->cas2;
    }

    /**
     * Cas2 Setter
     * @param mixed $cas2
     * @return Protocol
     */
    public function setCas2($cas2)
    {
        $this->cas2 = $cas2;
        return $this;
    }

    /**
     * Extras Getter
     * @return mixed
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Extras Setter
     * @param mixed $extras
     * @return Protocol
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;
        return $this;
    }

    /**
     * Key Getter
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Key Setter
     * @param mixed $key
     * @return Protocol
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Value Getter
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Value Setter
     * @param mixed $value
     * @return Protocol
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * 数据解包
     * @param $binaryPackage
     */
    public function unpack($binaryPackage)
    {
        // 解开数据的头部
        $format = 'Cmagic/Copcode/nkeylength/Cextralength/Cdatatype/nstatus/Nbodylength/NOpaque/NCAS1/NCAS2';
        $header = unpack($format, $binaryPackage);
        $this->setCas1($header['CAS1']);
        $this->setCas2($header['CAS2']);
        $this->setMagic($header['magic']);
        $this->setOpcode($header['opcode']);
        $this->setStatus($header['status']);
        $this->setOpaque($header['Opaque']);
        $this->setDataType($header['datatype']);

        // 除了头部还有其他的内容
        if ($header['bodylength']) {
            $bodyLength = $header['bodylength'];
            $data = substr($binaryPackage, 24, $bodyLength);

            // 解开额外信息
            if ($header['extralength']) {
                $extraUnpacked = unpack('Nint', substr($data, 0, $header['extralength']));
                $this->extras = $extraUnpacked['int'];
            }
            $this->key = substr($data, $header['extralength'], $header['keylength']);
            $this->value = substr($data, $header['extralength'] + $header['keylength']);
        }
    }

    /**
     * 数据打包
     * @return false|string
     */
    public function __toString()
    {
        // 计算包各部分的长度
        $keyLength = $this->getKey() ? strlen($this->getKey()) : 0x00;
        $valueLength = $this->getValue() ? strlen($this->getValue()) : 0x00;
        $extrasLength = $this->getExtras() ? strlen($this->getExtras()) : 0x00;
        $totalBodyLength = $keyLength + $valueLength + $extrasLength;

        // 打包数据头部二进制
        $package = pack('CCnCCnNNNN', 0x80, $this->getOpcode(), $keyLength,
            $extrasLength, null, null,
            $totalBodyLength,
            $this->getOpaque(),
            $this->getCas1(),
            $this->getCas2()
        );

        // 拼接包体数据为完整包
        $this->getExtras() && $package .= $this->getExtras();
        $this->getKey() && $package .= $this->getKey();
        $this->getValue() && $package .= $this->getValue();
        return $package;
    }
}