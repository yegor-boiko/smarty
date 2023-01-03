<?php
/**
 * Smarty Internal Plugin Compile Break
 * Compiles the {break} tag
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

/**
 * Smarty Internal Plugin Compile Break Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class BreakTag extends Base {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $optional_attributes = ['levels'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $shorttag_order = ['levels'];

	/**
	 * Tag name may be overloaded by ContinueTag
	 *
	 * @var string
	 */
	protected $tag = 'break';

	/**
	 * Compiles code for the {break} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 *
	 * @return string compiled code
	 * @throws \Smarty\CompilerException
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = array(), $tag = null, $function = null)
	{
		[$levels, $foreachLevels] = $this->checkLevels($args, $compiler);
		$output = "<?php ";
		if ($foreachLevels > 0 && $this->tag === 'continue') {
			$foreachLevels--;
		}
		if ($foreachLevels > 0) {
			/* @var ForeachTag $foreachCompiler */
			$foreachCompiler = $compiler->getTagCompiler('foreach');
			$output .= $foreachCompiler->compileRestore($foreachLevels);
		}
		$output .= "{$this->tag} {$levels};?>";
		return $output;
	}

	/**
	 * check attributes and return array of break and foreach levels
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 *
	 * @return array
	 * @throws \Smarty\CompilerException
	 */
	public function checkLevels($args, \Smarty\Compiler\Template $compiler) {
		static $_is_loopy = ['for' => true, 'foreach' => true, 'while' => true, 'section' => true];
		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		if ($_attr['nocache'] === true) {
			$compiler->trigger_template_error('nocache option not allowed', null, true);
		}
		if (isset($_attr['levels'])) {
			if (!is_numeric($_attr['levels'])) {
				$compiler->trigger_template_error('level attribute must be a numeric constant', null, true);
			}
			$levels = $_attr['levels'];
		} else {
			$levels = 1;
		}
		$level_count = $levels;
		$stack_count = count($compiler->_tag_stack) - 1;
		$foreachLevels = 0;
		$lastTag = '';
		while ($level_count > 0 && $stack_count >= 0) {
			if (isset($_is_loopy[$compiler->_tag_stack[$stack_count][0]])) {
				$lastTag = $compiler->_tag_stack[$stack_count][0];
				if ($level_count === 0) {
					break;
				}
				$level_count--;
				if ($compiler->_tag_stack[$stack_count][0] === 'foreach') {
					$foreachLevels++;
				}
			}
			$stack_count--;
		}
		if ($level_count !== 0) {
			$compiler->trigger_template_error("cannot {$this->tag} {$levels} level(s)", null, true);
		}
		if ($lastTag === 'foreach' && $this->tag === 'break' && $foreachLevels > 0) {
			$foreachLevels--;
		}
		return [$levels, $foreachLevels];
	}
}