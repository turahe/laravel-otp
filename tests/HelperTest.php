<?php
namespace Turahe\Otp\Test;

class HelperTest extends TestCase
{

    /**
     * @dataProvider providerFormatPhoneNumbersData
     */
    public function testFormatPhoneNumbers($expected, $actual)
    {
        $number = format_phone($actual);
        $this->assertEquals($expected, $number);
    }

    public function providerFormatPhoneNumbersData()
    {
        return [
            '+6285212341234' => '+62812341234',
            '085212341234' => '+682812341234',
            '0852 1234 1234' => '+682812341234',
        ];
    }

    /**
     * @dataProvider providerFormatNumbersWhatsAppData
     */
    public function testFormatNumbersWhatsApp($expected, $actual)
    {
        $number = format_whatsapp($actual);
        $this->assertEquals($expected, $number);
    }

    public function providerFormatNumbersWhatsAppData()
    {
        return [
            '+6285212341234' => 62812341234,
            '085212341234' => 682812341234,
            '0852 1234 1234' => 682812341234,
        ];
    }
}
