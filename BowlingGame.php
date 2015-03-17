<?php

/**
 * See http://carlosbuenosvinos.com/kata-bowling-game/
 * 
 * Scoring Bowling: The game consists of 10 frames as shown above.  
 * In each frame the player has two opportunities to knock down 10 pins.  
 * The score for the frame is the total number of pins knocked down, 
 * plus bonuses for strikes and spares.
 * 
 * A spare is when the player knocks down all 10 pins in two tries.
 * The bonus for that frame is the number of pins knocked down by the
 * next roll.  So in frame 3 above, the score is 10 (the total number
 * knocked down) plus a bonus of 5 (the number of pins knocked down
 * on the next roll.)
 * 
 * A strike is when the player knocks down all 10 pins on his first
 * try.  The bonus for that frame is the value of the next two balls
 * rolled.
 * 
 * In the tenth frame a player who rolls a spare or strike is allowed
 * to roll the extra balls to complete the frame.  However no more than
 * three balls can be rolled in tenth frame.
 * 
 * Note that the extra balls awarded only count towards the bonus for
 * preceding strike or spare - they do not count themselves. So if the 
 * tenth frame is 10, 10, 10 it scores 30. If it's 5, 5, 5 it scores
 * 15 points.
 * 
 * -------------
 * 
 * Write a class named “Game” that has two methods:
 * - roll(pins : int) is called each time the player rolls a ball.  The
 * argument is the number of pins knocked down.
 * - score() : int is called only at the very end of the game.  It 
 * returns the total score for that game.
 * 
 * Resolve applying TDD, 100% code coverage is possible. When finish, 
 * comment on this post with your GitHub repository (or similiar)
 * solution so others can check.
 * 
 * @package default
 */

class Game {

    private $rollsLeftInFrame;
    private $currentFrame;
    private $frames;

    public function __construct() {
        $this->currentFrame = 0;
        $this->initialiseFrame();
    }

    private function initialiseFrame() {
        $this->currentFrame++;
        if($this->currentFrame < 10) {
            $this->rollsLeftInFrame = 2;
        } else {
            // Allow a possible 3 rolls in the tenth frame, but the third roll is taken away if they don't at least get a spare.
            $this->rollsLeftInFrame = 3; 
        }

        if($this->currentFrame <= 10) {
            // Start a new array to store the rolls
            if(!isset($this->frames)) $this->frames = array();
            $this->frames[] = array();
        }
    }

    /**
     * Sum the next N rolls. Used for calculating the bonus for strikes or spares.
     * @param integer $frameIndex The index of the frame containing the starting roll
     * @param integer $rollIndex The index of the roll to start at (counting starts at the next roll)
     * @param integer $n 
     * @return integer The sum of the rolls
     */
    private function getSumOfNextNRolls($frameIndex, $rollIndex, $n) {
        $sum = 0;
        $rollIndex++; // Start at the roll after the current one.
        for(; $frameIndex < count($this->frames) && $n > 0; $frameIndex++, $rollIndex = 0) {
            $frame = $this->frames[$frameIndex];
            for(; $rollIndex < count($frame) && $n > 0; $rollIndex++) {
                $sum += $frame[$rollIndex];
                $n--;
            }
        }
        return $sum;
    }

    public function score() {
        if($this->currentFrame <= 10) {
            throw new LogicException('Do not call the score method until the 10 frames have been rolled.');
        }

        $gameTotal = 0;

        for($frameIndex = 0; $frameIndex < count($this->frames); $frameIndex++) {
            $frame = $this->frames[$frameIndex];
            $frameScore = 0;

            // Calculate the score for each roll. For strikes and spares look forward to add in bonuses.
            for($rollIndex = 0; $rollIndex < count($frame); $rollIndex++) {
                $roll = $frame[$rollIndex];
                $bonus = 0;

                if($rollIndex == 0 && $roll == 10) {
                    // Strike - Add the next two roll scores as bonuses
                    // In all frames a strike on the first roll is awarded bonuses from the next two rolls.
                    $bonus = $this->getSumOfNextNRolls($frameIndex, $rollIndex, 2);
                } elseif($rollIndex == 1 && $roll + $frame[0] == 10) {
                    // Spare - Add the next roll score as a bonus
                    $bonus = $this->getSumOfNextNRolls($frameIndex, $rollIndex, 1);
                }

                // In the tenth frame, the bonus balls awarded for the first strike or spare do not score themselves.
                if($frameIndex == 9 && $frame[0] == 10 && $rollIndex > 0) $roll = 0;
                if($frameIndex == 9 && $rollIndex == 2) $roll = 0;

                $frameScore += $roll + $bonus;
            }

            $gameTotal += $frameScore;
        }

        return $gameTotal;
    }

    /**
     * Processes each roll. It does not calculate the game score, that is done in the call to score().
     * It validates the roll based on the current state of the game.
     * It maintains state with the rollsLeftInFrame and currentFrame members.
     * @param integer $roll 
     * @return None
     */
    public function roll($roll) {
        if(!is_int($roll)) {
            throw new InvalidArgumentException('$roll must be an integer.');
        }

        if($this->currentFrame > 10) {
            throw new LogicException('The 10 frames have been rolled, do not call this method any more, thanks.');
        }

        // Calculate the max possible value for $roll based on the current state of the game.
        $max_roll = 10; // Start with 10 and reduce according to state.

        $currentFramesRolls =& $this->frames[count($this->frames) - 1];

        // The rules for frames 1 - 9 and frame 10 are different.
        if($this->currentFrame < 10) {
            // If this roll is the first of a frame, then the max is 10.
            // If it's the second then the max is the number of pins after the previous roll.
            if($this->rollsLeftInFrame == 1) {
                $max_roll = 10 - $currentFramesRolls[0];
            }
        } else {
            // Frame 10, potentially 3 rolls are allowed.
            // Note: Other code will prevent us incorrectly getting to the third roll without getting a strike or spare first.
            if($this->rollsLeftInFrame == 1) {
                // On the third ball 
                // If the first two rolls were strikes then the max is now 10.
                // If a spare was achieved with the first two rolls then the max is now 10.
                // If the first ball was a strike and the second wasn't, then the max now is the remaining pins.
                // Use the modulo operator to see if there are any pins left standing, then use that to set the max.
                $pins_knocked_down = ($currentFramesRolls[0] + $currentFramesRolls[1]) % 10;
                $max_roll = 10 - $pins_knocked_down;
            } elseif($this->rollsLeftInFrame == 2) {
                // On the second ball
                // Use the modulo operator to calculate the max pins left, since the first ball could have been a strike.
                $pins_knocked_down = $currentFramesRolls[0] % 10;
                $max_roll = 10 - $pins_knocked_down;
            }

            // On the first roll the max is 10, which is the default.
        }

        // Validate the roll by the correct limits.
        if($roll < 0 || $roll > $max_roll) {
            throw new InvalidArgumentException('$roll should be between 0 and ' . $max_roll . '.');
        }

        // Calculate if the current frame is complete.
        $frame_is_complete = false;
        if($this->currentFrame < 10) {
            // If this roll is the last, or if a strike was rolled, the frame is complete
            if($this->rollsLeftInFrame == 1 || $roll == 10) {
                $frame_is_complete = true;
            }
        } else {
            // Frame 10, potentially 3 rolls are allowed.
            if($this->rollsLeftInFrame == 1) {
                // The third ball is always the last 
                $frame_is_complete = true;
            } elseif($this->rollsLeftInFrame == 2) {
                // The second ball is the last if the player fails to get a strike or spare.
                if($currentFramesRolls[0] != 10 && $currentFramesRolls[0] + $roll != 10) {
                    $frame_is_complete = true;
                }
            }
        }

        // Store the roll at the end of the current frame.
        $currentFramesRolls[] = $roll;

        if($frame_is_complete) {
            $this->initialiseFrame();
        } else {
            $this->rollsLeftInFrame--;
        }
    }
}
