<?php
/**
 * Smarty Internal Plugin Compile Setfilter
 * Compiles code for setfilter tag
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

/**
 * Smarty Internal Plugin Compile Setfilterclose Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class SetfilterClose extends Base {

	/**
	 * Compiles code for the {/setfilter} tag
	 * This tag does not generate compiled output. It resets variable filter.
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 *
	 * @return string compiled code
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null) {
		$this->getAttributes($compiler, $args);

		// reset variable filter to previous state
		$compiler->getSmarty()->setAutoModifiers(
			count($compiler->variable_filter_stack) ? array_pop($compiler->variable_filter_stack) : []
		);

		// this tag does not return compiled code
		$compiler->has_code = false;
		return true;
	}
}