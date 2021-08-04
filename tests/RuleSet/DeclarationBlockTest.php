<?php

namespace Sabberworm\CSS\Tests\RuleSet;

use PHPUnit\Framework\TestCase;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\Settings;
use Sabberworm\CSS\Value\Size;

/**
 * @covers \Sabberworm\CSS\RuleSet\DeclarationBlock
 */
class DeclarationBlockTest extends TestCase
{
    /**
     * @test
     *
     * @param string $shorthand
     * @param string $expanded
     *
     * @dataProvider borderShorthandProvider
     */
    public function expandBorderShorthandExpandsBorderShorthandNotation($shorthand, $expanded)
    {
        $shorthandDeclarationBlock = "body { $shorthand }";
        $parserState = new ParserState($shorthandDeclarationBlock, Settings::create());
        $declarationBlock = DeclarationBlock::parse($parserState);

        $declarationBlock->expandBorderShorthand();

        $expandedDeclarationBlock = 'body {' . $expanded . '}';
        self::assertSame($expandedDeclarationBlock, (string)$declarationBlock);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function borderShorthandProvider()
    {
        return [
            'none' => ['border: none;', 'border-style: none;'],
            'width' => ['border: 2px;', 'border-width: 2px;'],
            'color' => ['border: #f00;', 'border-color: #f00;'],
            'width and style' => ['border: 1em solid;', 'border-width: 1em;border-style: solid;'],
            'margin' => ['margin: 1em;', 'margin: 1em;'],
            'width, style and color' => [
                'border: 2px solid #000;',
                'border-width: 2px;border-style: solid;border-color: #000;',
            ],
        ];
    }

    /**
     * @test
     *
     * @param string $sCss
     * @param string $sExpected
     *
     * @dataProvider expandFontShorthandProvider
     */
    public function expandFontShorthand($sCss, $sExpected)
    {
        $parser = new Parser($sCss);
        $document = $parser->parse();
        foreach ($document->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->expandFontShorthand();
        }
        self::assertSame(trim((string)$document), $sExpected);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function expandFontShorthandProvider()
    {
        return [
            [
                'body{ margin: 1em; }',
                'body {margin: 1em;}',
            ],
            [
                'body {font: 12px serif;}',
                'body {font-style: normal;font-variant: normal;font-weight: normal;font-size: 12px;'
                . 'line-height: normal;font-family: serif;}',
            ],
            [
                'body {font: italic 12px serif;}',
                'body {font-style: italic;font-variant: normal;font-weight: normal;font-size: 12px;'
                . 'line-height: normal;font-family: serif;}',
            ],
            [
                'body {font: italic bold 12px serif;}',
                'body {font-style: italic;font-variant: normal;font-weight: bold;font-size: 12px;'
                . 'line-height: normal;font-family: serif;}',
            ],
            [
                'body {font: italic bold 12px/1.6 serif;}',
                'body {font-style: italic;font-variant: normal;font-weight: bold;font-size: 12px;'
                . 'line-height: 1.6;font-family: serif;}',
            ],
            [
                'body {font: italic small-caps bold 12px/1.6 serif;}',
                'body {font-style: italic;font-variant: small-caps;font-weight: bold;font-size: 12px;'
                . 'line-height: 1.6;font-family: serif;}',
            ],
        ];
    }

    /**
     * @test
     *
     * @param string $sCss
     * @param string $sExpected
     *
     * @dataProvider expandBackgroundShorthandProvider
     */
    public function expandBackgroundShorthand($sCss, $sExpected)
    {
        $parser = new Parser($sCss);
        $document = $parser->parse();
        foreach ($document->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->expandBackgroundShorthand();
        }
        self::assertSame(trim((string)$document), $sExpected);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function expandBackgroundShorthandProvider()
    {
        return [
            ['body {border: 1px;}', 'body {border: 1px;}'],
            [
                'body {background: #f00;}',
                'body {background-color: #f00;background-image: none;background-repeat: repeat;'
                . 'background-attachment: scroll;background-position: 0% 0%;}',
            ],
            [
                'body {background: #f00 url("foobar.png");}',
                'body {background-color: #f00;background-image: url("foobar.png");background-repeat: repeat;'
                . 'background-attachment: scroll;background-position: 0% 0%;}',
            ],
            [
                'body {background: #f00 url("foobar.png") no-repeat;}',
                'body {background-color: #f00;background-image: url("foobar.png");background-repeat: no-repeat;'
                . 'background-attachment: scroll;background-position: 0% 0%;}',
            ],
            [
                'body {background: #f00 url("foobar.png") no-repeat center;}',
                'body {background-color: #f00;background-image: url("foobar.png");background-repeat: no-repeat;'
                . 'background-attachment: scroll;background-position: center center;}',
            ],
            [
                'body {background: #f00 url("foobar.png") no-repeat top left;}',
                'body {background-color: #f00;background-image: url("foobar.png");background-repeat: no-repeat;'
                . 'background-attachment: scroll;background-position: top left;}',
            ],
        ];
    }

    /**
     * @test
     *
     * @param string $sCss
     * @param string $sExpected
     *
     * @dataProvider expandDimensionsShorthandProvider
     */
    public function expandDimensionsShorthand($sCss, $sExpected)
    {
        $parser = new Parser($sCss);
        $document = $parser->parse();
        foreach ($document->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->expandDimensionsShorthand();
        }
        self::assertSame(trim((string)$document), $sExpected);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function expandDimensionsShorthandProvider()
    {
        return [
            ['body {border: 1px;}', 'body {border: 1px;}'],
            ['body {margin-top: 1px;}', 'body {margin-top: 1px;}'],
            ['body {margin: 1em;}', 'body {margin-top: 1em;margin-right: 1em;margin-bottom: 1em;margin-left: 1em;}'],
            [
                'body {margin: 1em 2em;}',
                'body {margin-top: 1em;margin-right: 2em;margin-bottom: 1em;margin-left: 2em;}',
            ],
            [
                'body {margin: 1em 2em 3em;}',
                'body {margin-top: 1em;margin-right: 2em;margin-bottom: 3em;margin-left: 2em;}',
            ],
        ];
    }

    /**
     * @test
     *
     * @param string $sCss
     * @param string $sExpected
     *
     * @dataProvider createBorderShorthandProvider
     */
    public function createBorderShorthand($sCss, $sExpected)
    {
        $parser = new Parser($sCss);
        $document = $parser->parse();
        foreach ($document->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->createBorderShorthand();
        }
        self::assertSame(trim((string)$document), $sExpected);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function createBorderShorthandProvider()
    {
        return [
            ['body {border-width: 2px;border-style: solid;border-color: #000;}', 'body {border: 2px solid #000;}'],
            ['body {border-style: none;}', 'body {border: none;}'],
            ['body {border-width: 1em;border-style: solid;}', 'body {border: 1em solid;}'],
            ['body {margin: 1em;}', 'body {margin: 1em;}'],
        ];
    }

    /**
     * @test
     *
     * @param string $sCss
     * @param string $sExpected
     *
     * @dataProvider createFontShorthandProvider
     */
    public function createFontShorthand($sCss, $sExpected)
    {
        $parser = new Parser($sCss);
        $document = $parser->parse();
        foreach ($document->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->createFontShorthand();
        }
        self::assertSame(trim((string)$document), $sExpected);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function createFontShorthandProvider()
    {
        return [
            ['body {font-size: 12px; font-family: serif}', 'body {font: 12px serif;}'],
            ['body {font-size: 12px; font-family: serif; font-style: italic;}', 'body {font: italic 12px serif;}'],
            [
                'body {font-size: 12px; font-family: serif; font-style: italic; font-weight: bold;}',
                'body {font: italic bold 12px serif;}',
            ],
            [
                'body {font-size: 12px; font-family: serif; font-style: italic; font-weight: bold; line-height: 1.6;}',
                'body {font: italic bold 12px/1.6 serif;}',
            ],
            [
                'body {font-size: 12px; font-family: serif; font-style: italic; font-weight: bold; '
                . 'line-height: 1.6; font-variant: small-caps;}',
                'body {font: italic small-caps bold 12px/1.6 serif;}',
            ],
            ['body {margin: 1em;}', 'body {margin: 1em;}'],
        ];
    }

    /**
     * @test
     *
     * @param string $sCss
     * @param string $sExpected
     *
     * @dataProvider createDimensionsShorthandProvider
     */
    public function createDimensionsShorthand($sCss, $sExpected)
    {
        $parser = new Parser($sCss);
        $document = $parser->parse();
        foreach ($document->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->createDimensionsShorthand();
        }
        self::assertSame(trim((string)$document), $sExpected);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function createDimensionsShorthandProvider()
    {
        return [
            ['body {border: 1px;}', 'body {border: 1px;}'],
            ['body {margin-top: 1px;}', 'body {margin-top: 1px;}'],
            ['body {margin-top: 1em; margin-right: 1em; margin-bottom: 1em; margin-left: 1em;}', 'body {margin: 1em;}'],
            [
                'body {margin-top: 1em; margin-right: 2em; margin-bottom: 1em; margin-left: 2em;}',
                'body {margin: 1em 2em;}',
            ],
            [
                'body {margin-top: 1em; margin-right: 2em; margin-bottom: 3em; margin-left: 2em;}',
                'body {margin: 1em 2em 3em;}',
            ],
        ];
    }

    /**
     * @test
     *
     * @param string $sCss
     * @param string $sExpected
     *
     * @dataProvider createBackgroundShorthandProvider
     */
    public function createBackgroundShorthand($sCss, $sExpected)
    {
        $parser = new Parser($sCss);
        $document = $parser->parse();
        foreach ($document->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->createBackgroundShorthand();
        }
        self::assertSame(trim((string)$document), $sExpected);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function createBackgroundShorthandProvider()
    {
        return [
            ['body {border: 1px;}', 'body {border: 1px;}'],
            ['body {background-color: #f00;}', 'body {background: #f00;}'],
            [
                'body {background-color: #f00;background-image: url(foobar.png);}',
                'body {background: #f00 url("foobar.png");}',
            ],
            [
                'body {background-color: #f00;background-image: url(foobar.png);background-repeat: no-repeat;}',
                'body {background: #f00 url("foobar.png") no-repeat;}',
            ],
            [
                'body {background-color: #f00;background-image: url(foobar.png);background-repeat: no-repeat;}',
                'body {background: #f00 url("foobar.png") no-repeat;}',
            ],
            [
                'body {background-color: #f00;background-image: url(foobar.png);background-repeat: no-repeat;'
                . 'background-position: center;}',
                'body {background: #f00 url("foobar.png") no-repeat center;}',
            ],
            [
                'body {background-color: #f00;background-image: url(foobar.png);background-repeat: no-repeat;'
                . 'background-position: top left;}',
                'body {background: #f00 url("foobar.png") no-repeat top left;}',
            ],
        ];
    }

    /**
     * @test
     */
    public function overrideRules()
    {
        $sCss = '.wrapper { left: 10px; text-align: left; }';
        $parser = new Parser($sCss);
        $document = $parser->parse();
        $oRule = new Rule('right');
        $oRule->setValue('-10px');
        $aContents = $document->getContents();
        $oWrapper = $aContents[0];

        self::assertCount(2, $oWrapper->getRules());
        $aContents[0]->setRules([$oRule]);

        $aRules = $oWrapper->getRules();
        self::assertCount(1, $aRules);
        self::assertSame('right', $aRules[0]->getRule());
        self::assertSame('-10px', $aRules[0]->getValue());
    }

    /**
     * @test
     */
    public function ruleInsertion()
    {
        $sCss = '.wrapper { left: 10px; text-align: left; }';
        $parser = new Parser($sCss);
        $document = $parser->parse();
        $aContents = $document->getContents();
        $oWrapper = $aContents[0];

        $oFirst = $oWrapper->getRules('left');
        self::assertCount(1, $oFirst);
        $oFirst = $oFirst[0];

        $oSecond = $oWrapper->getRules('text-');
        self::assertCount(1, $oSecond);
        $oSecond = $oSecond[0];

        $oBefore = new Rule('left');
        $oBefore->setValue(new Size(16, 'em'));

        $oMiddle = new Rule('text-align');
        $oMiddle->setValue(new Size(1));

        $oAfter = new Rule('border-bottom-width');
        $oAfter->setValue(new Size(1, 'px'));

        $oWrapper->addRule($oAfter);
        $oWrapper->addRule($oBefore, $oFirst);
        $oWrapper->addRule($oMiddle, $oSecond);

        $aRules = $oWrapper->getRules();

        self::assertSame($oBefore, $aRules[0]);
        self::assertSame($oFirst, $aRules[1]);
        self::assertSame($oMiddle, $aRules[2]);
        self::assertSame($oSecond, $aRules[3]);
        self::assertSame($oAfter, $aRules[4]);

        self::assertSame(
            '.wrapper {left: 16em;left: 10px;text-align: 1;text-align: left;border-bottom-width: 1px;}',
            $document->render()
        );
    }

    /**
     * @test
     */
    public function orderOfElementsMatchingOriginalOrderAfterExpandingShorthands()
    {
        $sCss = '.rule{padding:5px;padding-top: 20px}';
        $parser = new Parser($sCss);
        $document = $parser->parse();
        $aDocs = $document->getAllDeclarationBlocks();

        self::assertCount(1, $aDocs);

        $oDeclaration = array_pop($aDocs);
        $oDeclaration->expandShorthands();

        // TODO: The order is different on PHP 5.6 than on PHP >= 7.0.
        self::assertEquals(
            [
                'padding-top' => 'padding-top: 20px;',
                'padding-right' => 'padding-right: 5px;',
                'padding-bottom' => 'padding-bottom: 5px;',
                'padding-left' => 'padding-left: 5px;',
            ],
            array_map('strval', $oDeclaration->getRulesAssoc())
        );
    }
}
