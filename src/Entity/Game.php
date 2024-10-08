<?php

namespace App\Entity;

use App\Enum\Piece;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private Piece $currentTurn = Piece::X;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, BoardField>
     */
    #[ORM\OneToMany(targetEntity: BoardField::class, mappedBy: 'game', cascade: ['persist', 'remove'])]
    private Collection $boardFields;

    #[ORM\OneToOne(mappedBy: 'game', cascade: ['persist', 'remove'])]
    private ?Score $score = null;

    public function __construct()
    {
        $this->boardFields = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBoard(): array
    {
        $boardArray = [];

        for ($i = 0; $i < BoardField::BOARD_SIZE; $i++) {
            for ($j = 0; $j < BoardField::BOARD_SIZE; $j++) {
                $boardArray[$i][$j] = Piece::NONE;
            }
        }

        foreach ($this->boardFields as $boardField) {
            $x = $boardField->getXPosition();
            $y = $boardField->getYPosition();
            $boardArray[$x][$y] = $boardField->getPiece();
        }

        return $boardArray;
    }

    public function getScore(): ?Score
    {
        return $this->score;
    }

    public function getBoardFields(): Collection
    {
        return $this->boardFields;
    }

    public function getCurrentTurn(): Piece
    {
        return $this->currentTurn;
    }

    public function setCurrentTurn(Piece $currentTurn): void
    {
        $this->currentTurn = $currentTurn;
    }
}
