<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Factory_CommandSets} class.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see \Mailcode\Mailcode_Factory_CommandSets
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory_CommandSets
{
    /**
     * @var Mailcode_Factory_CommandSets_Set_If|NULL
     */
    private ?Mailcode_Factory_CommandSets_Set_If $if = null;
    
    /**
     * @var Mailcode_Factory_CommandSets_Set_Show|NULL
     */
    private ?Mailcode_Factory_CommandSets_Set_Show $show = null;
    
    /**
     * @var Mailcode_Factory_CommandSets_Set_Misc|NULL
     */
    private ?Mailcode_Factory_CommandSets_Set_Misc $misc = null;
    
   /**
    * @var Mailcode_Factory_CommandSets_Set_Set|NULL
    */
    private ?Mailcode_Factory_CommandSets_Set_Set $set = null;
    
   /**
    * @var Mailcode_Factory_CommandSets_Set_ElseIf|NULL
    */
    private ?Mailcode_Factory_CommandSets_Set_ElseIf $elseIf = null;
    
    public function if() : Mailcode_Factory_CommandSets_Set_If
    {
        if(!isset($this->if))
        {
            $this->if = new Mailcode_Factory_CommandSets_Set_If();
        }
        
        return $this->if;
    }
    
    public function elseIf() : Mailcode_Factory_CommandSets_Set_ElseIf
    {
        if(!isset($this->elseIf))
        {
            $this->elseIf = new Mailcode_Factory_CommandSets_Set_ElseIf();
        }
        
        return $this->elseIf;
    }
    
    public function show() : Mailcode_Factory_CommandSets_Set_Show
    {
        if(!isset($this->show))
        {
            $this->show = new Mailcode_Factory_CommandSets_Set_Show();
        }
        
        return $this->show;
    }
    
    public function misc() : Mailcode_Factory_CommandSets_Set_Misc
    {
        if(!isset($this->misc))
        {
            $this->misc = new Mailcode_Factory_CommandSets_Set_Misc();
        }
        
        return $this->misc;
    }
    
    public function set() : Mailcode_Factory_CommandSets_Set_Set
    {
        if(!isset($this->set))
        {
            $this->set = new Mailcode_Factory_CommandSets_Set_Set();
        }
        
        return $this->set;
    }
}
