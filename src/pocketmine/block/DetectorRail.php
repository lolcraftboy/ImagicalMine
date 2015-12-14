<?php

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
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\entity\Minecart;

class DetectorRail extends RailBlock{

	protected $id = self::DETECTOR_RAIL;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Detector Rail";
	}

	public function getHardness(){
		return 0.1;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$down = $this->getSide(0);
		if($down->isTransparent() === false){
				$this->getLevel()->setBlock($block, Block::get(Item::POWERED_RAIL, 0), true, true);
			return true;
		}
		return false;
	}
	
	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_SCHEDULED){
			if($this->meta === 1 && !$this->isEntityCollided(Minecart)){
				$this->meta =0;
				$this->getLevel()->setBlock($this, Block::get($this->getId(), $this->meta), false, true, true);
				return Level::BLOCK_UPDATE_WEAK;
			}
		}
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$this->getLevel()->scheduleUpdate($this, 50);
		}
		return false;
	}

	public function onEntityCollide(Entity $entity){
		if(!$this->isPowered()){
			$this->togglePowered();
		}
	}

	public function getDrops(Item $item){
		return [[Item::DETECTOR_RAIL, 0, 1]];
	}

	public function isPowered(){
		return (($this->meta & 0x01) === 0x01);
	}
	
	public function isEntityCollided(Entity $entity = null){
		foreach ($this->getLevel()->getEntities() as $entity){
			if($entity instanceof Minecart && $entity->getPosition() === $this)
				return true;
		}
		return false;
	}

	/**
	 * Toggles the current state of this plate
	 */
	public function togglePowered(){
		$this->meta ^= 0x08;
		$this->isPowered()?$this->power=15:$this->power=0;
		$this->getLevel()->setBlock($this, $this, true, true);
	}
}
