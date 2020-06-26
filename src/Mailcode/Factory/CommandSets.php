<?php
/**
 * File containing the {@see Mailcode_Factory} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Factory
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory_CommandSets
{
    /**
     * @var Mailcode_Factory_CommandSets_Set_If
     */
    private $if;
    
    /**
     * @var Mailcode_Factory_CommandSets_Set_Show
     */
    private $show;
    
    /**
     * @var Mailcode_Factory_CommandSets_Set_Misc
     */
    private $misc;
    
   /**
    * @var Mailcode_Factory_CommandSets_Set_Set
    */
    private $set;
    
   /**
    * @var Mailcode_Factory_CommandSets_Set_ElseIf
    */
    private $elseIf;
    
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
