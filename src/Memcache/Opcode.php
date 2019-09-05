<?php

namespace SwooleKit\Cache\Memcache;

use EasySwoole\Spl\SplEnum;

/**
 * Memcache Opcode
 * Class Opcode
 * @package Memcache
 */
class Opcode extends SplEnum
{
    const OP_GET = 0x00;
    const OP_SET = 0x01;
    const OP_ADD = 0x02;
    const OP_REPLACE = 0x03;
    const OP_DELETE = 0x04;
    const OP_INCREMENT = 0x05;
    const OP_DECREMENT = 0x06;
    const OP_QUIT = 0x07;
    const OP_FLUSH = 0x08;
    const OP_GET_Q = 0x09;
    const OP_NO_OP = 0x0a;
    const OP_VERSION = 0x0b;
    const OP_GET_K = 0x0c;
    const OP_GET_K_Q = 0x0d;
    const OP_APPEND = 0x0e;
    const OP_PREPEND = 0x0f;

    const OP_STAT = 0x10;
    const OP_SET_Q = 0x11;
    const OP_ADD_Q = 0x12;
    const OP_REPLACE_Q = 0x13;
    const OP_DELETE_Q = 0x14;
    const OP_INCREMENT_Q = 0x15;
    const OP_DECREMENT_Q = 0x16;
    const OP_QUIT_Q = 0x17;
    const OP_FLUSH_Q = 0x18;
    const OP_APPEND_Q = 0x19;
    const OP_PREPEND_Q = 0x1a;
    const OPX_VERBOSITY = 0x1b;
    const OPX_TOUCH = 0x1c;
    const OPX_GET = 0x1d;
    const OPX_GET_Q = 0x1e;

    const OP_SASL_LIST_MECHS = 0x20;
    const OP_SASL_AUTH = 0x21;
    const OP_SASL_STEP = 0x22;

    const OP_R_GET = 0x30;
    const OP_R_SET = 0x31;
    const OP_R_SET_Q = 0x32;
    const OP_R_APPEND = 0x33;
    const OP_R_APPEND_Q = 0x34;
    const OP_R_PREPEND = 0x35;
    const OP_R_PREPEND_Q = 0x36;
    const OP_R_DELETE = 0x37;
    const OP_R_DELETE_Q = 0x38;
    const OP_R_INCR = 0x39;
    const OP_R_INCR_Q = 0x3a;
    const OP_R_DECR = 0x3b;
    const OP_R_DECR_Q = 0x3c;
    const OPX_SET_V_BUCKET = 0x3d;
    const OPX_GET_V_BUCKET = 0x3e;
    const OPX_DEL_V_BUCKET = 0x3f;

    const OPX_TAP_CONNECT = 0x40;
    const OPX_TAP_MUTATION = 0x41;
    const OPX_TAP_DELETE = 0x42;
    const OPX_TAP_FLUSH = 0x43;
    const OPX_TAP_OPAQUE = 0x44;
    const OPX_TAP_V_BUCKET_SET = 0x45;
    const OPX_TAP_CHECKPOINT_START = 0x46;
    const OPX_TAP_CHECKPOINT_END = 0x47;

}