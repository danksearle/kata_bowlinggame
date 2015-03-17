<?php

require "BowlingGame.php";

class BowlingGameTest extends PHPUnit_Framework_TestCase
{
    /**
     * Process an array of frames, each frame is an array of rolls.
     * @param Game $game 
     * @param array $frames
     */
    private function processFrames($game, $frames) {
        foreach($frames as $frame) {
            foreach($frame as $roll) {
                $game->roll($roll);
            }
        }
    }

    /*
     * Tests to cover the input validation for roll
     */
    public function testExceptionGoodFrames1()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(6, 0), // 6
                array(1, 2), // 9
                array(10),   // 29
                array(10),   // 39
                array(0, 0), // 39
                array(10),   // 58
                array(3, 6), // 67
                array(2, 8), // 85
                array(8, 2), // 96
                array(1, 1), // 98
            ));
        }
        catch (Exception $e) {
            $this->fail('An unexpected exception has been raised. ' . $e->getMessage());
        }
        $this->assertEquals(98, $game->score());

        return;
    }
    public function testGivenGameExample()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(1, 4), // 5
                array(4, 5), // 14
                array(6, 4),   // 29
                array(5, 5),   // 49
                array(10), // 60
                array(0, 1),   // 61
                array(7, 3), // 77
                array(6, 4), // 97
                array(10), // 117
                array(2, 8, 6), // 133
            ));
        }
        catch (Exception $e) {
            $this->fail('An unexpected exception has been raised. ' . $e->getMessage());
        }
        $this->assertEquals(133, $game->score()); // Test with rolls from the given example.

        return;
    }
    public function testPerfectGame()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(10),
                array(10),
                array(10),
                array(10),
                array(10),
                array(10),
                array(10),
                array(10),
                array(10),
                array(10, 10, 10),
            ));
        }
        catch (Exception $e) {
            $this->fail('An unexpected exception has been raised. ' . $e->getMessage());
        }
        $this->assertEquals(300, $game->score());

        return;
    }
    public function testPerfectTenthFrame()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(10, 10, 10),
            ));
        }
        catch (Exception $e) {
            $this->fail('An unexpected exception has been raised. ' . $e->getMessage());
        }
        $this->assertEquals(30, $game->score());

        return;
    }
    public function testTenthFrameSpareScoring()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(5, 5, 5),
            ));
        }
        catch (Exception $e) {
            $this->fail('An unexpected exception has been raised. ' . $e->getMessage());
        }
        $this->assertEquals(15, $game->score());

        return;
    }
    public function testExceptionGoodFrames4()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
            ));
        }
        catch (Exception $e) {
            $this->fail('An unexpected exception has been raised. ' . $e->getMessage());
        }
        $this->assertEquals(0, $game->score());

        return;
    }
    public function testExceptionGoodFrames5()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(1, 0),
                array(1, 0),
                array(1, 0),
                array(1, 0),
                array(1, 0),
                array(1, 0),
                array(1, 0),
                array(1, 0),
                array(1, 0),
                array(1, 0),
            ));
        }
        catch (Exception $e) {
            $this->fail('An unexpected exception has been raised. ' . $e->getMessage());
        }
        $this->assertEquals(10, $game->score());

        return;
    }

    /*
     * Test invalid roll values.
     */
    public function testExceptionInRoll1()
    {
        $game = new Game;
        try {
            $game->roll("hello");
        }
        catch (InvalidArgumentException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }
    public function testExceptionInRoll2()
    {
        $game = new Game;
        try {
            $game->roll(-1);
        }
        catch (InvalidArgumentException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }
    public function testExceptionInRoll3()
    {
        $game = new Game;
        try {
            $game->roll(11);
        }
        catch (InvalidArgumentException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }
    public function testExceptionInRoll4()
    {
        $game = new Game;
        try {
            $game->roll(1.5);
        }
        catch (InvalidArgumentException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    /*
     * Test that a second roll cannot add up to more than ten pins when combined with the 
     * previous one, unless the previous roll completed a frame.
     */
    public function testExceptionInRoll5()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(5, 6), // Can't roll a 5 after a 6
            ));
        }
        catch (InvalidArgumentException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }
    public function testExceptionInRoll6()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(3, 5),
                array(1, 4),
                array(5, 6), // Can't roll a 6 after a 5
            ));
        }
        catch (InvalidArgumentException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }
    public function testExceptionInRoll7()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(3, 5),
                array(6, 4),
                array(10), 
                array(5, 6), // Can't roll a 6 after a 5
            ));
        }
        catch (InvalidArgumentException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }
    public function testExceptionTooManyRolls1()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0, 0), // One roll too many
            ));
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }
    public function testExceptionTooManyRolls2()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(10, 10, 10, 0), // One roll too many
            ));
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }
    public function testExceptionTooManyRolls3()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0, 10, 0), // One roll too many
            ));
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }
    public function testExceptionTooManyRolls4()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(5, 5, 10, 0), // One roll too many
            ));
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }
    public function testExceptionTooManyRolls5()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(5, 5, 5, 0), // One roll too many
            ));
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }



    /*
     * Tests to cover the input validation for score
     */
    public function testExceptionInScoreCall1()
    {
        $game = new Game;
        try {
            $score = $game->score(); // Can't call score yet
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }
    public function testExceptionInScoreCall2()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0),
            ));
            $score = $game->score(); // Can't call score yet
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }
    public function testExceptionInScoreCall3()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(3, 5),
                array(6, 4),
                array(10), 
                array(5, 3),
            ));
            $score = $game->score(); // Can't call score yet
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }
    public function testExceptionInScoreCall4()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(5, 5),
            ));
            $score = $game->score(); // Can't call score yet (player got a spare left to use)
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }
    public function testExceptionInScoreCall5()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(10),
            ));
            $score = $game->score(); // Can't call score yet (player got a strike left to use)
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }
    public function testExceptionInScoreCall6()
    {
        $game = new Game;
        try {
            $this->processFrames($game, array(
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(0, 0),
                array(10, 10),
            ));
            $score = $game->score(); // Can't call score yet (player got a strike left to use)
        }
        catch (LogicException $expected) {
            return;
        }

        $this->fail('An expected LogicException has not been raised.');
    }
}
