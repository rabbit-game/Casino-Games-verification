<?php
	function createCards($server_seed) 
	{
		$all_cards = range(0, (52 - 1));
		$cards = array();
		$hash = hash("sha512", $server_seed);
		for ($index = 0; $index < 6; $index++)
		{
			for ($i = 0; $i < 52; $i++)
			{
				array_push($cards, array("card" => $all_cards[$i], "hash" => $hash));
				$hash = substr($hash, 1) . $hash[0];
			}
			$hash = hash("sha512", $hash);
		}
		usort($cards, function ($a, $b) {return (strcmp($a["hash"], $b["hash"]) >= 1);});
		return array("cards" => array_column($cards, "card"), "hash" => $hash);
	}
	function pullCard($cards, $index)
	{
		$suits = array("♣", "♠", "♥", "♦");
		$color = array("black", "black", "red", "red");
		$scores = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 0, 0, 0);
		$numbers = array('A', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'J', 'Q', 'K');
		$card = $cards[$index];
		return array("suit" => $suits[$card / 13], "num" => $numbers[$card % 13], "score" => $scores[$card % 13], "color" => $color[$card / 13]);
	}
	function claculateHandScore($hand)
	{
		$score = 0;
		foreach ($hand as $card)
			$score += $card["score"];
		return ($score % 10);
	}
	function playing($cards)
	{
		$BANKER_TABLE = array(
			0 => array(true, true, true, true, true, true, true, true, true, true),
			1 => array(true, true, true, true, true, true, true, true, true, true),
			2 => array(true, true, true, true, true, true, true, true, true, true),
			3 => array(true, true, true, true, true, true, true, true, false, true),
			4 => array(false, false, true, true, true, true, true, true, false, false),
			5 => array(false, false, false, false, true, true, true, true, false, false),
			6 => array(false, false, false, false, false, false, true, true, false, false),
			7 => array(false, false, false, false, false, false, false, false, false, false),
		);
		$index = 0;
			
		$card = pullCard($cards, $index);
		$player_hand = array($card);
		$index++;
		
		$card = pullCard($cards, $index);
		$banker_hand = array($card);
		$index++;
		
		$card = pullCard($cards, $index);
		array_push($player_hand, $card);
		$index++;
		
		$card = pullCard($cards, $index);
		array_push($banker_hand, $card);
		$index++;
		
		$player_hand_score = claculateHandScore($player_hand);
		$banker_hand_score = claculateHandScore($banker_hand);
		
		$player_natural_hand = false;
		$banker_natural_hand = false;
		
		if ($player_hand_score == 8 or $player_hand_score == 9)
			$player_natural_hand = true;
		if ($banker_hand_score == 8 or $banker_hand_score == 9)
			$banker_natural_hand = true;
		
		if (!$player_natural_hand AND !$banker_natural_hand AND $player_hand_score <= 5)
		{
			$card = pullCard($cards, $index);
			array_push($player_hand, $card);
			$index++;
			
			$player_hand_score = claculateHandScore($player_hand);
			
			if ($BANKER_TABLE[$banker_hand_score][$player_hand_score])
			{
				$card = pullCard($cards, $index);
				array_push($banker_hand, $card);
				$index++;
				
				$banker_hand_score = claculateHandScore($banker_hand);
			}
		}
		
		return array(
					"player" => array("hand" => $player_hand, "score" => $player_hand_score),
					"banker" => array("hand" => $banker_hand, "score" => $banker_hand_score)
					);
	}
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Baccarat verify</title>
    <link rel="stylesheet" href="./lib/main.css" />
    <link rel="stylesheet" href="./lib/bootstrap/css/bootstrap.min.css" />
    <style>
      .card-list {
        display: flex;
      }
      .cardbox {
        height: auto;
        padding: 0;
        zoom: 0.8;
      }
      .cardbox:hover .card {
        transform: unset;
      }
    </style>
  </head>
  <body>
    <div id="app" class="main">
      <h1 class="text-center pb-5">Baccarat verify</h1>
      <hr />
      <form class="py-5" method="POST" action="baccarat.php">
        <h2 class="text-center pb-5">Input</h2>
        <div class="form-group">
          <label>Server Seed</label>
          <input
		    type="text"
            class="form-control"
            name="server_seed"
          />
        </div>
      </form>
	  <?php
			if (isset($_POST["server_seed"]) and !empty($_POST["server_seed"]))
			{
				$server_seed = @$_POST["server_seed"];
				$result = createCards($server_seed);
				$hash = $result['hash'];
				$cards = $result['cards'];
				$result = playing($cards);
				echo '<hr />';
				echo '<form class="py-5">';
				echo '<h2 class="text-center pb-5">Results</h2>';
				echo '<div class="form-group" style="overflow-x: auto;">';
				echo ' <label>Server Seed: <br>'.$server_seed.'</label><br>';
				echo '<hr />';
				echo '<label>Game\'s hash: '.$hash.'</label><br>';
				echo '<hr />';
				echo '<label>Banker: </label>';
				$banker_hand = $result["banker"]["hand"];
				echo '<div class="card-list">';
				for	($i = 0; $i < count($banker_hand); $i++)
				{
					echo '<div class="cardbox '.$banker_hand[$i]["color"].'">';
					echo '<div class="card">';
					echo '<div class="flower">'.$banker_hand[$i]["suit"].'</div>';
					echo '<div class="point">'.$banker_hand[$i]["num"].'</div>';
					echo '</div>';
					echo '</div>';
				}
				echo '</div>';
				echo '<label>score: '.$result["banker"]["score"].'</label>';
				echo '<hr />';
				echo '<label>Player: </label>';
				$player_hand = $result["player"]["hand"];
				echo '<div class="card-list">';
				for	($i = 0; $i < count($player_hand); $i++)
				{
					echo '<div class="cardbox '.$player_hand[$i]["color"].'">';
					echo '<div class="card">';
					echo '<div class="flower">'.$player_hand[$i]["suit"].'</div>';
					echo '<div class="point">'.$player_hand[$i]["num"].'</div>';
					echo '</div>';
					echo '</div>';
				}
				echo '</div>';
				echo '<label>score: '.$result["player"]["score"].'</label>';
				echo '</div>';
				echo '</form>';
			}
		?>
     </div>
  </body>
</html>

