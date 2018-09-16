<?php
////////////////////////////////////////////////////////////////////////////////
// __________ __             ________                   __________              
// \______   \  |__ ______  /  _____/  ____ _____ ______\______   \ _______  ___
//  |     ___/  |  \\____ \/   \  ____/ __ \\__  \\_  __ \    |  _//  _ \  \/  /
//  |    |   |   Y  \  |_> >    \_\  \  ___/ / __ \|  | \/    |   (  <_> >    < 
//  |____|   |___|  /   __/ \______  /\___  >____  /__|  |______  /\____/__/\_ \
//                \/|__|           \/     \/     \/             \/            \/
// -----------------------------------------------------------------------------
//          Designed and Developed by Brad Jones <brad @="bjc.id.au" />         
// -----------------------------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////

class StringFunctionsTest extends PHPUnit_Framework_TestCase
{
	protected $string = 'This is a string.';

	public function testIsUTF8()
	{
		$this->assertTrue(Gears\Str\isUTF8(''));
		$this->assertTrue(Gears\Str\isUTF8('éééé'));
		$this->assertFalse(Gears\Str\isUTF8(utf8_decode('éééé')));
	}

	public function testToUTF8()
	{
		$this->assertEquals('', Gears\Str\toUTF8(''));
		$this->assertEquals('éééé', Gears\Str\toUTF8(utf8_decode('éééé')));
	}

	public function testToLatin1()
	{
		$this->assertEquals('', Gears\Str\toLatin1(''));
		$this->assertEquals('éééé', Gears\Str\toLatin1(utf8_encode('éééé')));
	}

	public function testFixUTF8()
	{
		$this->assertEquals('', Gears\Str\fixUTF8(''));
		$this->assertEquals('Fédération Camerounaise de Football', Gears\Str\fixUTF8('FÃÂ©dération Camerounaise de Football'));
		$this->assertEquals('Fédération Camerounaise de Football', Gears\Str\fixUTF8('FÃ©dÃ©ration Camerounaise de Football'));
		$this->assertEquals('Fédération Camerounaise de Football', Gears\Str\fixUTF8('FÃÂ©dÃÂ©ration Camerounaise de Football'));
		$this->assertEquals('Fédération Camerounaise de Football', Gears\Str\fixUTF8('FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football'));
	}

	public function testWildCardMatch()
	{
		$html = '<a title="foo" href="/hello">Hello World</a>';

		$pattern = '<a*href="*"*>*</a>';

		$matches = array
		(
			0 => array
			(
				0 => '<a title="foo" href="/hello">Hello World</a>',
			),
			1 => array
			(
				0 => ' title="foo" ',
			),
			2 => array
			(
				0 => '/hello',
			),
			3 => array
			(
				0 => '',
			),
			4 => array
			(
				0 => 'Hello World',
			)
		);

		$this->assertEquals($matches, Gears\Str\wildCardMatch($html, $pattern));
	}

	public function testSearch()
	{
		$this->assertEquals(8, Gears\Str\search($this->string, 'a'));
		$this->assertEquals(-1, Gears\Str\search($this->string, 'b'));
		$this->assertEquals(4, Gears\Str\search($this->string, '/ is /', true));
		$this->assertEquals(-1, Gears\Str\search($this->string, '/ foo /', true));
	}

	public function testReplace()
	{
		$this->assertEquals('This is a Gears\Str.', Gears\Str\replace($this->string, 'string', 'Gears\Str'));
		$this->assertEquals($this->string, Gears\Str\replace($this->string, 'foo', 'Gears\Str'));
		$this->assertEquals('This is a Gears\Str.', Gears\Str\replace($this->string, '/string/', 'Gears\Str', true));
		$this->assertEquals($this->string, Gears\Str\replace($this->string, '/ string /', 'Gears\Str', true));
		$this->assertEquals('This is a STRING.', Gears\Str\replace($this->string, '/string/', function($matches){ return strtoupper($matches[0]); }, true));
	}

	public function testMatch()
	{
		$this->assertEquals(['is a', 'is a'], Gears\Str\match($this->string.' '.$this->string, '/is a/'));
		$this->assertEquals([], Gears\Str\match($this->string.' '.$this->string, '/is foo/'));
	}

	public function testBetween()
	{
		$this->assertEquals($this->string, Gears\Str\between('<start>'.$this->string.'</end>', '<start>', '</end>'));
		$this->assertEquals('', Gears\Str\between('<start>'.$this->string.'</end>', '<start1>', '</end>'));
		$this->assertEquals('', Gears\Str\between('<start>'.$this->string.'</end>', '<start>', '</end1>'));
		$this->assertEquals('<start>'.$this->string.'</end>', Gears\Str\between('<start>'.$this->string.'</end>', '<start>', '</end>', true));
	}

	public function testBetweenRegx()
	{
		$xml = file_get_contents(__DIR__.'/data/books.xml');
		$expected = require(__DIR__.'/data/books.php');
		$this->assertEquals($expected, Gears\Str\betweenRegx($xml, '<author>', '</author>'));
	}

	public function testSubString()
	{
		$this->assertEquals('is a', Gears\Str\substring($this->string, 5, 9));
		$this->assertEquals('is a string.', Gears\Str\substring($this->string, 5));
	}

	public function testSlice()
	{
		$this->assertEquals('is a', Gears\Str\slice($this->string, 5, 9));
		$this->assertEquals('is a string.', Gears\Str\slice($this->string, 5));
	}

	public function testConCat()
	{
		$this->assertEquals($this->string.$this->string, Gears\Str\conCat($this->string, $this->string));
	}

	public function testSplit()
	{
		$this->assertEquals(['T','h','i','s',' ','i','s',' ','a',' ','s','t','r','i','n','g','.'], Gears\Str\split($this->string));
		$this->assertEquals(['This','is','a','string.'], Gears\Str\split($this->string, ' '));
	}

	public function testRange()
	{
		$this->assertEquals(true, Gears\Str\range($this->string, 0, 20));
		$this->assertEquals(true, Gears\Str\range($this->string, 5, 20));
		$this->assertEquals(false, Gears\Str\range($this->string, 0, 10));
		$this->assertEquals(false, Gears\Str\range($this->string, 30, 40));
	}

	public function testCharAt()
	{
		$this->assertEquals('T', Gears\Str\charAt($this->string, 0));
		$this->assertEquals('h', Gears\Str\charAt($this->string, 1));
		$this->assertEquals('i', Gears\Str\charAt($this->string, 2));
		$this->assertEquals('s', Gears\Str\charAt($this->string, 3));
	}

	public function testCharCodeAt()
	{
		$this->assertEquals(84, Gears\Str\charCodeAt($this->string, 0));
		$this->assertEquals(104, Gears\Str\charCodeAt($this->string, 1));
		$this->assertEquals(105, Gears\Str\charCodeAt($this->string, 2));
		$this->assertEquals(115, Gears\Str\charCodeAt($this->string, 3));
	}

	public function testFromCharCode()
	{
		$this->assertEquals('T', Gears\Str\fromCharCode(84));
		$this->assertEquals('h', Gears\Str\fromCharCode(104));
		$this->assertEquals('i', Gears\Str\fromCharCode(105));
		$this->assertEquals('s', Gears\Str\fromCharCode(115));
	}

	public function testIndexOf()
	{
		$this->assertEquals(2, Gears\Str\indexOf($this->string, 'is'));
		$this->assertEquals(false, Gears\Str\indexOf($this->string, 'foo'));
	}

	public function testLastIndexOf()
	{
		$this->assertEquals(5, Gears\Str\lastIndexOf($this->string, 'is'));
		$this->assertEquals(false, Gears\Str\lastIndexOf($this->string, 'foo'));
	}

	public function testAscii()
	{
		$this->assertEquals('', Gears\Str\ascii(''));
		$this->assertEquals('deja vu', Gears\Str\ascii('déjà vu'));
		$this->assertEquals('i', Gears\Str\ascii('ı'));
		$this->assertEquals('a', Gears\Str\ascii('ä'));
	}

	public function testContains()
	{
		$this->assertTrue(Gears\Str\contains('taylor', 'ylo'));
		$this->assertTrue(Gears\Str\contains('taylor', array('ylo')));
		$this->assertFalse(Gears\Str\contains('taylor', 'xxx'));
		$this->assertFalse(Gears\Str\contains('taylor', array('xxx')));
		$this->assertFalse(Gears\Str\contains('taylor', ''));
	}

	public function testStartsWith()
	{
		$this->assertTrue(Gears\Str\startsWith('jason', 'jas'));
		$this->assertTrue(Gears\Str\startsWith('jason', 'jason'));
		$this->assertTrue(Gears\Str\startsWith('jason', array('jas')));
		$this->assertFalse(Gears\Str\startsWith('jason', 'day'));
		$this->assertFalse(Gears\Str\startsWith('jason', array('day')));
		$this->assertFalse(Gears\Str\startsWith('jason', ''));
	}

	public function testEndsWith()
	{
		$this->assertTrue(Gears\Str\endsWith('jason', 'on'));
		$this->assertTrue(Gears\Str\endsWith('jason', 'jason'));
		$this->assertTrue(Gears\Str\endsWith('jason', array('on')));
		$this->assertFalse(Gears\Str\endsWith('jason', 'no'));
		$this->assertFalse(Gears\Str\endsWith('jason', array('no')));
		$this->assertFalse(Gears\Str\endsWith('jason', ''));
		$this->assertFalse(Gears\Str\endsWith('7', ' 7'));
	}

	public function testFinish()
	{
		$this->assertEquals('abbc', Gears\Str\finish('ab', 'bc'));
		$this->assertEquals('abbc', Gears\Str\finish('abbcbc', 'bc'));
		$this->assertEquals('abcbbc', Gears\Str\finish('abcbbcbc', 'bc'));
	}

	public function testIs()
	{
		$this->assertTrue(Gears\Str\is('/', '/'));
		$this->assertFalse(Gears\Str\is('/', ' /'));
		$this->assertFalse(Gears\Str\is('/', '/a'));
		$this->assertTrue(Gears\Str\is('foo/*', 'foo/bar/baz'));
		$this->assertTrue(Gears\Str\is('*/foo', 'blah/baz/foo'));
	}

	public function testLength()
	{
		$this->assertEquals(17, Gears\Str\length($this->string));
	}

	public function testLimit()
	{
		$this->assertEquals('This...', Gears\Str\limit($this->string, 4));
	}

	public function testLower()
	{
		$this->assertEquals('this is a string.', Gears\Str\lower($this->string));
	}

	public function testUpper()
	{
		$this->assertEquals('THIS IS A STRING.', Gears\Str\upper($this->string));
	}

	public function testWords()
	{
		$this->assertEquals('Taylor...', Gears\Str\words('Taylor Otwell', 1));
		$this->assertEquals('Taylor___', Gears\Str\words('Taylor Otwell', 1, '___'));
		$this->assertEquals('Taylor Otwell', Gears\Str\words('Taylor Otwell', 3));
		$this->assertEquals(' Taylor Otwell ', Gears\Str\words(' Taylor Otwell ', 3));
		$this->assertEquals(' Taylor...', Gears\Str\words(' Taylor Otwell ', 1));
		$nbsp = chr(0xC2).chr(0xA0);
		$this->assertEquals(' ', Gears\Str\words(' '));
		$this->assertEquals($nbsp, Gears\Str\words($nbsp));
	}

	public function testPlural()
	{
		$this->assertEquals('children', Gears\Str\plural('child'));
		$this->assertEquals('tests', Gears\Str\plural('test'));
		$this->assertEquals('deer', Gears\Str\plural('deer'));
		$this->assertEquals('Children', Gears\Str\plural('Child'));
		$this->assertEquals('CHILDREN', Gears\Str\plural('CHILD'));
		$this->assertEquals('Tests', Gears\Str\plural('Test'));
		$this->assertEquals('TESTS', Gears\Str\plural('TEST'));
		$this->assertEquals('tests', Gears\Str\plural('test'));
		$this->assertEquals('Deer', Gears\Str\plural('Deer'));
		$this->assertEquals('DEER', Gears\Str\plural('DEER'));
	}

	public function testSingular()
	{
		$this->assertEquals('Child', Gears\Str\singular('Children'));
		$this->assertEquals('CHILD', Gears\Str\singular('CHILDREN'));
		$this->assertEquals('Test', Gears\Str\singular('Tests'));
		$this->assertEquals('TEST', Gears\Str\singular('TESTS'));
		$this->assertEquals('Deer', Gears\Str\singular('Deer'));
		$this->assertEquals('DEER', Gears\Str\singular('DEER'));
		$this->assertEquals('Criterion', Gears\Str\singular('Criteria'));
		$this->assertEquals('CRITERION', Gears\Str\singular('CRITERIA'));
		$this->assertEquals('child', Gears\Str\singular('children'));
		$this->assertEquals('test', Gears\Str\singular('tests'));
		$this->assertEquals('deer', Gears\Str\singular('deer'));
		$this->assertEquals('criterion', Gears\Str\singular('criteria'));
	}

	public function testRandom()
	{
		$this->assertEquals(16, strlen(Gears\Str\random()));
		$randomInteger = mt_rand(1, 100);
		$this->assertEquals($randomInteger, strlen(Gears\Str\random($randomInteger)));
		$this->assertInternalType('string', Gears\Str\random());
	}

	public function testTitle()
	{
		$this->assertEquals('Jefferson Costella', Gears\Str\title('jefferson costella'));
		$this->assertEquals('Jefferson Costella', Gears\Str\title('jefFErson coSTella'));
	}

	public function testSlug()
	{
		$this->assertEquals('hello-world', Gears\Str\slug('hello world'));
		$this->assertEquals('hello-world', Gears\Str\slug('hello-world'));
		$this->assertEquals('hello-world', Gears\Str\slug('hello_world'));
		$this->assertEquals('hello_world', Gears\Str\slug('hello_world', '_'));
	}

	public function testSnake()
	{
		$this->assertEquals('foo_bar', Gears\Str\snake('fooBar'));
	}

	public function testCamelCase()
	{
		$this->assertEquals('fooBar', Gears\Str\camel('FooBar'));
		$this->assertEquals('fooBar', Gears\Str\camel('foo_bar'));
		$this->assertEquals('fooBarBaz', Gears\Str\camel('Foo-barBaz'));
		$this->assertEquals('fooBarBaz', Gears\Str\camel('foo-bar_baz'));
	}

	public function testStudly()
	{
		$this->assertEquals('FooBar',  Gears\Str\studly('fooBar'));
		$this->assertEquals('FooBar',  Gears\Str\studly('foo_bar'));
		$this->assertEquals('FooBarBaz',  Gears\Str\studly('foo-barBaz'));
		$this->assertEquals('FooBarBaz',  Gears\Str\studly('foo-bar_baz'));
	}
}