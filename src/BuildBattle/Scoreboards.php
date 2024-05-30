<?php

declare(strict_types=1);

namespace BuildBattle;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;
use pocketmine\Server;

class Scoreboards
{

	public static array $scoreboards = [];

	public static function new(Player $player, string $objectiveName, string $displayName): void
	{
		if (isset(self::$scoreboards[$player->getName()])) {
			self::remove($player);
		}
		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = "sidebar";
		$pk->objectiveName = $objectiveName;
		$pk->displayName = $displayName;
		$pk->criteriaName = "dummy";
		$pk->sortOrder = 0;
		$player->sendDataPacket($pk);
		self::$scoreboards[$player->getName()] = $objectiveName;
	}

	public static function remove(Player $player): void
	{
		$objectiveName = self::getObjectiveName($player);
		$pk = new RemoveObjectivePacket();
		$pk->objectiveName = $objectiveName;
		$player->sendDataPacket($pk);
		unset(self::$scoreboards[$player->getName()]);
	}

	public static function getObjectiveName(Player $player): ?string
	{
		return self::$scoreboards[$player->getName()] ?? null;
	}

	public static function setLine(Player $player, int $score, string $message): void
	{
		if (!isset(self::$scoreboards[$player->getName()])) {
			Server::getInstance()->getLogger()->error("Cannot set a score to a player with no scoreboard");
			return;
		}
		if ($score > 15 || $score < 1) {
			Server::getInstance()->getLogger()->error("Score must be between the value of 1-15. $score out of range");
			return;
		}
		$objectiveName = self::getObjectiveName($player);
		$entry = new ScorePacketEntry();
		$entry->objectiveName = $objectiveName;
		$entry->type = $entry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = $score;
		$entry->scoreboardId = $score;
		$pk = new SetScorePacket();
		$pk->type = $pk::TYPE_CHANGE;
		$pk->entries[] = $entry;
		$player->sendDataPacket($pk);
	}
}
