<?php
	function createHashDiceRoll($server_seed, $client_seed)
	{
		$hash = hash("sha512", $server_seed);
		$hash = hash_hmac("sha512", $client_seed, $hash);
		$roll = 0;
		$index = 0;
		while (($roll <= 1000000) and ($index < strlen($hash)))
		{
			$roll = hexdec(substr($hash, $index, 5));
			$index += 5;
		}
		if ($roll > 1000000)
			$roll = $roll % 1000000;
		return array("roll" => $roll, "hash" => $hash);
	}
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>HashDice verify</title>
    <link rel="stylesheet" href="./lib/main.css" />
    <link rel="stylesheet" href="./lib/bootstrap/css/bootstrap.min.css" />
  </head>
  <body>
    <div id="app" class="main">
      <h1 class="text-center pb-5">HashDice verify</h1>
      <hr />
      <form class="py-5" method="POST" action="hashdice.php">
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
				$result = createHashDiceRoll($server_seed, $client_seed);
				$hash = $result['hash'];
				$roll = $result['roll'];
				echo '<hr />';
				echo '<form class="py-5">';
				echo '<h2 class="text-center pb-5">Results</h2>';
				echo '<div class="form-group" style="overflow-x: auto;">';
				echo ' <label>Server Seed: <br>'.$server_seed.'</label><br>';
				echo '<hr />';
				echo ' <label>Client Seed: <br>'.$client_seed.'</label><br>';
				echo '<hr />';
				echo '<label>Game\'s hash: <br>'.$hash.'</label><br>';
				echo '<hr />';
				echo '<label>Roll: <br>'.$roll.'</label><br>';
				echo '<hr />';
				echo '</div>';
				echo '</form>';
			}
		?>
     </div>
  </body>
</html>

