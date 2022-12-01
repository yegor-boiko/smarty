<?php

namespace Smarty\Compile;

/**
 * Smarty Internal Plugin Compile Shared Inheritance
 * Shared methods for {extends} and {block} tags
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Shared Inheritance Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
abstract class Inheritance extends \Smarty\Compile\Base
{
    /**
     * Compile inheritance initialization code as prefix
     *
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     * @param bool|false                            $initChildSequence if true force child template
     */
    public static function postCompile(\Smarty_Internal_TemplateCompilerBase $compiler, $initChildSequence = false)
    {
        $compiler->prefixCompiledCode .= "<?php \$_smarty_tpl->_loadInheritance();\n\$_smarty_tpl->inheritance->init(\$_smarty_tpl, " .
                                         var_export($initChildSequence, true) . ");\n?>\n";
    }

    /**
     * Register post compile callback to compile inheritance initialization code
     *
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     * @param bool|false                            $initChildSequence if true force child template
     */
    public function registerInit(\Smarty_Internal_TemplateCompilerBase $compiler, $initChildSequence = false)
    {
        if ($initChildSequence || !isset($compiler->_cache[ 'inheritanceInit' ])) {
            $compiler->registerPostCompileCallback(
                array(self::class, 'postCompile'),
                array($initChildSequence),
                'inheritanceInit',
                $initChildSequence
            );
            $compiler->_cache[ 'inheritanceInit' ] = true;
        }
    }
}
