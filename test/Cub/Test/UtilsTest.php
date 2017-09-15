<?php
class Cub_Test_UtilsTest extends PHPUnit_Framework_TestCase
{
    public function testUrlEncode()
    {
        $this->assertEquals('',
            Cub_Utils::urlEncode(array(
                # Empty array should be encoded to empty string
            ))
        );
        $this->assertEquals("foo=1&bar=baz",
            Cub_Utils::urlEncode(array(
                'foo' => 1,
                'bar' => 'baz',
            ))
        );
        $this->assertEquals("foo=bar&baz__key1=val1&baz__key2=val2",
            Cub_Utils::urlEncode(array(
                'foo' => 'bar',
                'baz' => array(
                    'key1' => 'val1',
                    'key2' => 'val2',
                )
            ))
        );
        $this->assertEquals("foo__bar__key1=val1&foo__bar__key2=val2",
            Cub_Utils::urlEncode(array(
                'foo' => array(
                    'bar' => array(
                        'key1' => 'val1',
                        'key2' => 'val2',
                    )
                )
            )
        ));
    }
}
