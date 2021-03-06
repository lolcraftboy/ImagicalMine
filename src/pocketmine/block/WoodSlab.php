<?php
/**
 * src/pocketmine/block/WoodSlab.php
 *
 * @package default
 */


/*
 *
 *  _                       _           _ __  __ _
 * (_)                     (_)         | |  \/  (_)
 *  _ _ __ ___   __ _  __ _ _  ___ __ _| | \  / |_ _ __   ___
 * | | '_ ` _ \ / _` |/ _` | |/ __/ _` | | |\/| | | '_ \ / _ \
 * | | | | | | | (_| | (_| | | (_| (_| | | |  | | | | | |  __/
 * |_|_| |_| |_|\__,_|\__, |_|\___\__,_|_|_|  |_|_|_| |_|\___|
 *                     __/ |
 *                    |___/
 *
 * This program is a third party build by ImagicalMine.
 *
 * PocketMine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author ImagicalMine Team
 * @link http://forums.imagicalcorp.ml/
 *
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class WoodSlab extends Transparent
{

    protected $id = self::WOOD_SLAB;

    /**
     *
     * @param unknown $meta (optional)
     */
    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }


    /**
     *
     * @return unknown
     */
    public function getHardness()
    {
        return 2;
    }


    /**
     *
     * @return unknown
     */
    public function getName()
    {
        static $names = [
            0 => "Oak",
            1 => "Spruce",
            2 => "Birch",
            3 => "Jungle",
            4 => "Acacia",
            5 => "Dark Oak",
            6 => "",
            7 => ""
        ];
        return (($this->meta & 0x08) === 0x08 ? "Upper " : "") . $names[$this->meta & 0x07] . " Wooden Slab";
    }


    /**
     *
     * @return unknown
     */
    protected function recalculateBoundingBox()
    {
        if (($this->meta & 0x08) > 0) {
            return new AxisAlignedBB(
                $this->x,
                $this->y + 0.5,
                $this->z,
                $this->x + 1,
                $this->y + 1,
                $this->z + 1
            );
        } else {
            return new AxisAlignedBB(
                $this->x,
                $this->y,
                $this->z,
                $this->x + 1,
                $this->y + 0.5,
                $this->z + 1
            );
        }
    }


    /**
     *
     * @param Item    $item
     * @param Block   $block
     * @param Block   $target
     * @param unknown $face
     * @param unknown $fx
     * @param unknown $fy
     * @param unknown $fz
     * @param Player  $player (optional)
     * @return unknown
     */
    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null)
    {
        $this->meta &= 0x07;
        if ($face === 0) {
            if ($target->getId() === self::WOOD_SLAB and ($target->getDamage() & 0x08) === 0x08 and ($target->getDamage() & 0x07) === ($this->meta & 0x07)) {
                $this->getLevel()->setBlock($target, Block::get(Item::DOUBLE_WOOD_SLAB, $this->meta), true);

                return true;
            } elseif ($block->getId() === self::WOOD_SLAB and ($block->getDamage() & 0x07) === ($this->meta & 0x07)) {
                $this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_WOOD_SLAB, $this->meta), true);

                return true;
            } else {
                $this->meta |= 0x08;
            }
        } elseif ($face === 1) {
            if ($target->getId() === self::WOOD_SLAB and ($target->getDamage() & 0x08) === 0 and ($target->getDamage() & 0x07) === ($this->meta & 0x07)) {
                $this->getLevel()->setBlock($target, Block::get(Item::DOUBLE_WOOD_SLAB, $this->meta), true);

                return true;
            } elseif ($block->getId() === self::WOOD_SLAB and ($block->getDamage() & 0x07) === ($this->meta & 0x07)) {
                $this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_WOOD_SLAB, $this->meta), true);

                return true;
            }
        } else { //TODO: collision
            if ($block->getId() === self::WOOD_SLAB) {
                if (($block->getDamage() & 0x07) === ($this->meta & 0x07)) {
                    $this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_WOOD_SLAB, $this->meta), true);

                    return true;
                }

                return false;
            } else {
                if ($fy > 0.5) {
                    $this->meta |= 0x08;
                }
            }
        }

        if ($block->getId() === self::WOOD_SLAB and ($target->getDamage() & 0x07) !== ($this->meta & 0x07)) {
            return false;
        }
        $this->getLevel()->setBlock($block, $this, true, true);

        return true;
    }


    /**
     *
     * @return unknown
     */
    public function getToolType()
    {
        return Tool::TYPE_AXE;
    }


    /**
     *
     * @param Item    $item
     * @return unknown
     */
    public function getDrops(Item $item)
    {
        return [
            [$this->id, $this->meta & 0x07, 1],
        ];
    }
}
