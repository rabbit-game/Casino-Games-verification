<?php
	function createCards($server_seed, $client_seed) 
	{
		$all_cards = range(0, (52 - 1));
		$cards = array();
		$hash = hash("sha512", $server_seed);
		$hash = hash_hmac("sha512", $client_seed, $hash);
		for ($index = 0; $index < 4; $index++)
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
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Hilo verify</title>
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
      <h1 class="text-center pb-5">Hilo verify</h1>
      <hr />
      <form class="py-5" method="POST" action="hilo.php">
        <h2 class="text-center pb-5">Input</h2>
        <div class="form-group">
          <label>Server Seed</label>
          <input
		    type="text"
            class="form-control"
            name="server_seed"
          />
		  <label>Client Seed</label>
          <input
		    type="text"
            class="form-control"
            name="client_seed"
          />
		  <br>
		  <input
		    type="submit"
            class="form-control"
            name="submit"
			value="Verify"
          />
        </div>
      </form>
	  <?php
			if (isset($_POST["server_seed"]) and !empty($_POST["server_seed"]) and isset($_POST["client_seed"]) and !empty($_POST["client_seed"]))
			{
				$server_seed = @$_POST["server_seed"];
				$client_seed = @$_POST["client_seed"];
				$result = createCards($server_seed, $client_seed);
				$hash = $result['hash'];
				$cards = $result['cards'];
				echo '<hr />';
				echo '<form class="py-5">';
				echo '<h2 class="text-center pb-5">Results</h2>';
				echo '<div class="form-group" style="overflow-x: auto;">';
				echo ' <label>Server Seed: <br>'.$server_seed.'</label><br>';
				echo '<hr />';
				echo ' <label>Client Seed: <br>'.$client_seed.'</label><br>';
				echo '<hr />';
				echo '<label>Game\'s hash: '.$hash.'</label><br>';
				echo '<hr />';
				echo '<div class="card-list">';
				for	($i = 0; $i < count($cards); $i++)
				{
					$card = pullCard($cards, $i);
					echo '<div class="cardbox '.$card["color"].'">';
					echo '<div class="card">';
					echo '<div class="flower">'.$card["suit"].'</div>';
					echo '<div class="point">'.$card["num"].'</div>';
					echo '</div>';
					echo '</div>';
				}
				echo '</div>';
				echo '</div>';
				echo '</form>';
			}
		?>
     </div>
  </body>
</html>

