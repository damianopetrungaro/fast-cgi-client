<?php

declare(strict_types=1);
/*
 * Copyright (c) 2010-2014 Pierrick Charron
 * Copyright (c) 2016-2018 Holger Woltersdorf
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace hollodotme\FastCGI\Tests\Unit\Encoders;

use hollodotme\FastCGI\Encoders\PacketEncoder;
use PHPUnit\Framework\TestCase;

class PacketEncoderTest extends TestCase
{
    /**
     * @param int    $type
     * @param string $content
     * @param int    $requestId
     * @param array  $expectedHeader
     *
     * @dataProvider packetContentProvider
     */
    public function testCanEncodeAndDecodePacket(int $type, string $content, int $requestId, array $expectedHeader): void
    {
        $packetEncoder = new PacketEncoder();

        $packet = $packetEncoder->encodePacket($type, $content, $requestId);

        $header = $packetEncoder->decodeHeader($packet);

        $this->assertEquals($expectedHeader, $header);
        $this->assertEquals(\mb_substr($packet, -1 * \mb_strlen($content)), $content);
    }

    public function packetContentProvider(): array
    {
        return [
            [
                4, 'test', 1,
                [
                    'version' => 1,
                    'type' => 4,
                    'requestId' => 1,
                    'contentLength' => 4,
                    'paddingLength' => 0,
                    'reserved' => 0,
                ],
            ],
            [
                5, 'çélinö~ß', 12,
                [
                    'version' => 1,
                    'type' => 5,
                    'requestId' => 12,
                    'contentLength' => 12,
                    'paddingLength' => 0,
                    'reserved' => 0,
                ],
            ],
        ];
    }
}
