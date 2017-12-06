<?php
/**
 * This class helps in indirectly testing the functionalities of CakeTestCase::assertTags
 *
 * @package       Cake.Test.Fixture
 */
class AssertTagsTestCase extends CakeTestCase {

/**
 * test that assertTags knows how to handle correct quoting.
 *
 * @return void
 */
	public function testAssertTagsQuotes() {
		$input = '<a href="/test.html" class="active">My link</a>';
		$pattern = [
			'a' => ['href' => '/test.html', 'class' => 'active'],
			'My link',
			'/a'
		];
		$this->assertTags($input, $pattern);

		$input = "<a href='/test.html' class='active'>My link</a>";
		$pattern = [
			'a' => ['href' => '/test.html', 'class' => 'active'],
			'My link',
			'/a'
		];
		$this->assertTags($input, $pattern);

		$input = "<a href='/test.html' class='active'>My link</a>";
		$pattern = [
			'a' => ['href' => 'preg:/.*\.html/', 'class' => 'active'],
			'My link',
			'/a'
		];
		$this->assertTags($input, $pattern);
	}

/**
 * testNumericValuesInExpectationForAssertTags
 *
 * @return void
 */
	public function testNumericValuesInExpectationForAssertTags() {
		$value = 220985;

		$input = '<p><strong>' . $value . '</strong></p>';
		$pattern = [
			'<p',
				'<strong',
					$value,
				'/strong',
			'/p'
		];
		$this->assertTags($input, $pattern);

		$input = '<p><strong>' . $value . '</strong></p><p><strong>' . $value . '</strong></p>';
		$pattern = [
			'<p',
				'<strong',
					$value,
				'/strong',
			'/p',
			'<p',
				'<strong',
					$value,
				'/strong',
			'/p',
		];
		$this->assertTags($input, $pattern);

		$input = '<p><strong>' . $value . '</strong></p><p id="' . $value . '"><strong>' . $value . '</strong></p>';
		$pattern = [
			'<p',
				'<strong',
					$value,
				'/strong',
			'/p',
			'p' => ['id' => $value],
				'<strong',
					$value,
				'/strong',
			'/p',
		];
		$this->assertTags($input, $pattern);
	}

/**
 * testBadAssertTags
 *
 * @return void
 */
	public function testBadAssertTags() {
		$input = '<a href="/test.html" class="active">My link</a>';
		$pattern = [
			'a' => ['hRef' => '/test.html', 'clAss' => 'active'],
			'My link2',
			'/a'
		];
		$this->assertTags($input, $pattern);
	}

/**
 * testBadAssertTags
 *
 * @return void
 */
	public function testBadAssertTags2() {
		$input = '<a href="/test.html" class="active">My link</a>';
		$pattern = [
			'<a' => ['href' => '/test.html', 'class' => 'active'],
			'My link',
			'/a'
		];
		$this->assertTags($input, $pattern);
	}

}
