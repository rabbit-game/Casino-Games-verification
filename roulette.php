<?php
	function rouletteWheelSelection($server_seed, $type)
	{
		$hash = hash("sha512", $server_seed);
		$max_value = 3800;
		if ($type == 'european')
			$max_value = 3700;
		$selected_number = $max_value;
		$index = 0;
		while (($selected_number >= $max_value) and ($index < strlen($hash) - 2))
		{
			$sub_hash = substr($hash, $index, 3);
			$selected_number = hexdec($sub_hash);
			$index++;
		}
		if ($selected_number < $max_value)
			$selected_number = $selected_number / 100;
		else
			$selected_number = hexdec($hash[count($hash) - 1]);
		$selected_number = floor($selected_number);
		if ($selected_number == 37)
			$selected_number = "00";
		return array("selected_number" => $selected_number, "hash" => $hash);
	}
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Roulette verify</title>
    <link rel="stylesheet" href="./lib/main.css" />
    <link rel="stylesheet" href="./lib/bootstrap/css/bootstrap.min.css" />
  </head>
  <body>
    <div id="app" class="main">
      <h1 class="text-center pb-5">Roulette verify</h1>
      <hr />
      <form class="py-5" method="POST" action="roulette.php">
        <h2 class="text-center pb-5">Input</h2>
        <div class="form-group">
          <label>Server Seed</label>
          <input
		    type="text"
            class="form-control"
            name="server_seed"
          />
		  <label>Roulette Type</label>
		  <select name="roulette_type" class="form-control">
			<option value="european">european</option>
			<option value="american">american</option>
		  </select>
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
			if (isset($_POST["server_seed"]) and !empty($_POST["server_seed"]) and isset($_POST["roulette_type"]) and !empty($_POST["roulette_type"]))
			{
				$server_seed = @$_POST["server_seed"];
				$type = @$_POST["roulette_type"];
				$result = rouletteWheelSelection($server_seed, $type);
				$hash = $result['hash'];
				$selected_number = $result['selected_number'];
				echo '<hr />';
				echo '<form class="py-5">';
				echo '<h2 class="text-center pb-5">Results</h2>';
				echo '<div class="form-group" style="overflow-x: auto;">';
				echo ' <label>Server Seed: <br>'.$server_seed.'</label><br>';
				echo '<hr />';
				echo ' <label>Roulette Type: <br>'.$type.'</label><br>';
				echo '<hr />';
				echo '<label>Game\'s hash: <br>'.$hash.'</label><br>';
				echo '<hr />';
				echo '<label>Selected numbers: <br>'.$selected_number.'</label><br>';
				echo '<hr />';
				echo '</div>';
				echo '</form>';
			}
		?>
     </div>
  </body>
</html>

