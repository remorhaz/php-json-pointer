<?php

namespace Remorhaz\JSONPointer\Test\Parser\Lexer;

use Remorhaz\JSONPointer\Parser\Lexer\Lexer;

class UnreadTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Lexer\Exception
     */
    public function testUninitializedUnreadingThrowsException()
    {
        Lexer::factory()->unreadToken();
    }


    /**
     * @expectedException \LogicException
     */
    public function testUninitializedUnreadingThrowsSplException()
    {
        Lexer::factory()->unreadToken();
    }


    /**
     */
    public function testReadingSameTokenAfterUnreading()
    {
        $lexer = Lexer::factory()->setText('/a');
        $firstToken = $lexer->readToken();
        $secondToken = $lexer
            ->unreadToken()
            ->readToken();
        $this->assertSame($firstToken, $secondToken, "Reading after unreading returns different tokens");
    }


    /**
     * @param int $tokenAmount
     * @param string $text
     * @dataProvider providerTokenAmountAndText
     */
    public function testUnreadingAllReadTokensNoException($tokenAmount, $text)
    {
        $lexer = Lexer::factory()->setText($text);
        for ($i = 0; $i < $tokenAmount; $i++) {
            $lexer->readToken();
        }
        for ($i = 0; $i < $tokenAmount; $i++) {
            $lexer->unreadToken();
        }
    }


    /**
     * @param int $tokenAmount
     * @param string $text
     * @dataProvider providerTokenAmountAndText
     * @expectedException \Remorhaz\JSONPointer\Parser\Lexer\Exception
     */
    public function testUnreadingMoreTokensThanWasReadThrowsException($tokenAmount, $text)
    {
        $lexer = Lexer::factory()->setText($text);
        for ($i = 0; $i < $tokenAmount; $i++) {
            $lexer->readToken();
        }
        for ($i = 0; $i <= $tokenAmount; $i++) {
            $lexer->unreadToken();
        }
    }


    /**
     * @param int $tokenAmount
     * @param string $text
     * @dataProvider providerTokenAmountAndText
     * @expectedException \LogicException
     */
    public function testUnreadingMoreTokensThanWasReadThrowsSplException($tokenAmount, $text)
    {
        $lexer = Lexer::factory()->setText($text);
        for ($i = 0; $i < $tokenAmount; $i++) {
            $lexer->readToken();
        }
        for ($i = 0; $i <= $tokenAmount; $i++) {
            $lexer->unreadToken();
        }
    }


    /**
     * @param int $tokenAmount
     * @param string $text
     * @dataProvider providerTokenAmountAndText
     */
    public function testNoEndAfterUnreadingLastToken($tokenAmount, $text)
    {
        $lexer = Lexer::factory()->setText($text);
        for ($i = 0; $i < $tokenAmount; $i++) {
            $lexer->readToken();
        }
        $lexer->unreadToken();
        $this->assertFalse($lexer->isEnd(), "End condition after unreading token to buffer");
    }


    public function providerTokenAmountAndText()
    {
        return [
            'singleToken' => [1, '/'],
            'multipleTokens' => [3, 'aб日'],
        ];
    }
}
