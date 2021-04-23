<?php
	function kenoCreateNumbers($server_seed)
	{
		$all_nums = range(1, 40);
		$hash = hash("sha512", $server_seed);
		for ($index = 0; $index < 2; $index++)
		{
			$nums = array();
			for ($i = 0; $i < 40; $i++)
			{
				array_push($nums, array("num" => $all_nums[$i], "hash" => $hash));
				$hash = substr($hash, 1) . $hash[0];
			}
			usort($nums, function ($a, $b) {return (strcmp($a["hash"], $b["hash"]) >= 1);});
			$all_nums = array_column($nums, "num");
			$hash = hash("sha512", $hash);
		}
		$final_num = array_slice($all_nums, 0, 10);
		sort($final_num);
		return array("selected_numbers" => $final_num, "hash" => $hash);
	}
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Keno verify</title>
    <link rel="stylesheet" href="./lib/main.css" />
    <link rel="stylesheet" href="./lib/bootstrap/css/bootstrap.min.css" />
  </head>
  <body>
    <div id="app" class="main">
      <h1 class="text-center pb-5">Keno verify</h1>
      <hr />
      <form class="py-5" method="POST" action="keno.php">
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
				$result = kenoCreateNumbers($server_seed);
				$hash = $result['hash'];
				$selected_numbers = $result['selected_numbers'];
				echo '<hr />';
				echo '<form class="py-5">';
				echo '<h2 class="text-center pb-5">Results</h2>';
				echo '<div class="form-group" style="overflow-x: auto;">';
				echo ' <label>Server Seed: <br>'.$server_seed.'</label><br>';
				echo '<hr />';
				echo '<label>Game\'s hash: <br>'.$hash.'</label><br>';
				echo '<hr />';
				echo '<label>Selected numbers: <br>{'.implode(", ", $selected_numbers).'}</label><br>';
				echo '<hr />';
				echo '</div>';
				echo '</form>';
			}
		?>
     </div>
  </body>
</html>

